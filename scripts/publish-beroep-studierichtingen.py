#!/usr/bin/env python3
"""Controleer en publiceer descriptions en intro's voor beroep_studierichting."""

from __future__ import annotations

import argparse
import html
import json
import subprocess
import sys
from pathlib import Path
from typing import Any
from urllib.parse import urlencode


BASE_URL = "https://thefieldservicecompany.nl"
RESOLVE = "thefieldservicecompany.nl:443:45.82.188.97"
TAXONOMY = "beroep_studierichting"
ENDPOINT = "/wp-json/tfsc-admin/v1/beroep-studierichting-content-20261019"


class PublishError(RuntimeError):
    pass


class Client:
    def __init__(self, netrc_file: str):
        self.netrc_file = netrc_file

    def request(
        self,
        method: str,
        path: str,
        payload: dict[str, Any] | None = None,
    ) -> Any:
        command = [
            "curl",
            "--silent",
            "--show-error",
            "--fail-with-body",
            "--resolve",
            RESOLVE,
            "--netrc-file",
            self.netrc_file,
            "--request",
            method,
            BASE_URL + path,
        ]
        if payload is not None:
            command.extend(
                [
                    "--header",
                    "Content-Type: application/json",
                    "--data-binary",
                    json.dumps(payload, ensure_ascii=False),
                ]
            )
        result = subprocess.run(command, capture_output=True, text=True)
        if result.returncode != 0:
            raise PublishError(
                f"{method} {path} mislukt: {result.stderr.strip()} {result.stdout.strip()}"
            )
        try:
            return json.loads(result.stdout)
        except json.JSONDecodeError as error:
            raise PublishError(f"Ongeldige JSON van {method} {path}") from error


def build_content(node: str) -> list[dict[str, Any]]:
    result = subprocess.run(
        [node, "scripts/build-beroep-studierichtingen.mjs"],
        capture_output=True,
        text=True,
    )
    if result.returncode != 0:
        raise PublishError(f"Contentbuild mislukt: {result.stderr.strip()}")
    items = json.loads(result.stdout)
    if not isinstance(items, list) or len(items) != 23:
        raise PublishError("Contentbuild bevat niet exact 23 studierichtingen")
    return items


def fetch_terms(client: Client) -> list[dict[str, Any]]:
    query = urlencode(
        {
            "context": "edit",
            "hide_empty": "false",
            "per_page": 100,
            "orderby": "id",
            "order": "asc",
        }
    )
    result = client.request("GET", f"/wp-json/wp/v2/{TAXONOMY}?{query}")
    if not isinstance(result, list):
        raise PublishError("Onverwacht antwoord bij ophalen studierichtingen")
    return result


def preflight(
    content: list[dict[str, Any]], terms: list[dict[str, Any]]
) -> tuple[dict[str, dict[str, Any]], dict[str, dict[str, Any]]]:
    content_by_slug = {item["slug"]: item for item in content}
    terms_by_slug = {item["slug"]: item for item in terms}
    if len(content_by_slug) != 23 or len(terms_by_slug) != 23:
        raise PublishError("Dubbele of ontbrekende slugs in bron of live taxonomie")
    if set(content_by_slug) != set(terms_by_slug):
        raise PublishError(
            "Bron- en liveslugs verschillen: "
            + json.dumps(
                {
                    "alleen_bron": sorted(set(content_by_slug) - set(terms_by_slug)),
                    "alleen_live": sorted(set(terms_by_slug) - set(content_by_slug)),
                },
                ensure_ascii=False,
            )
        )
    for slug, item in content_by_slug.items():
        if html.unescape(terms_by_slug[slug]["name"]) != item["name"]:
            raise PublishError(f"Naamverschil bij {slug}")
        if int(terms_by_slug[slug].get("parent", 0)) != 0:
            raise PublishError(f"Onverwachte parent bij {slug}")
    return content_by_slug, terms_by_slug


def intro_snippet() -> str:
    return r"""
function tfsc_20261019_study_snapshot() {
    if ( ! function_exists( 'pods' ) || ! taxonomy_exists( 'beroep_studierichting' ) ) {
        return new WP_Error( 'dependency_missing', 'Pods of taxonomy ontbreekt.', [ 'status' => 500 ] );
    }
    $terms = get_terms( [
        'taxonomy' => 'beroep_studierichting',
        'hide_empty' => false,
    ] );
    if ( is_wp_error( $terms ) ) {
        return $terms;
    }
    $rows = [];
    foreach ( $terms as $term ) {
        $pod = pods( 'beroep_studierichting', (int) $term->term_id );
        if ( ! $pod || ! $pod->exists() ) {
            return new WP_Error( 'pod_missing', 'Pod ontbreekt voor ' . $term->slug, [ 'status' => 500 ] );
        }
        $rows[] = [
            'id' => (int) $term->term_id,
            'slug' => $term->slug,
            'intro' => (string) $pod->field( 'intro_taxonomy' ),
        ];
    }
    usort( $rows, function( $a, $b ) { return strcmp( $a['slug'], $b['slug'] ); } );
    return $rows;
}

add_action( 'rest_api_init', function() {
    register_rest_route( 'tfsc-admin/v1', '/beroep-studierichting-content-20261019', [
        [
            'methods' => 'GET',
            'permission_callback' => function() { return current_user_can( 'manage_options' ); },
            'callback' => function() {
                $rows = tfsc_20261019_study_snapshot();
                return is_wp_error( $rows ) ? $rows : rest_ensure_response( $rows );
            },
        ],
        [
            'methods' => 'POST',
            'permission_callback' => function() { return current_user_can( 'manage_options' ); },
            'callback' => function( WP_REST_Request $request ) {
                $params = $request->get_json_params();
                $items = isset( $params['items'] ) && is_array( $params['items'] )
                    ? $params['items']
                    : [];
                $current = tfsc_20261019_study_snapshot();
                if ( is_wp_error( $current ) ) { return $current; }
                if ( count( $items ) !== 23 || count( $current ) !== 23 ) {
                    return new WP_Error( 'count_mismatch', 'Verwacht exact 23 intro’s.', [ 'status' => 409 ] );
                }
                $current_by_slug = [];
                foreach ( $current as $row ) { $current_by_slug[ $row['slug'] ] = $row; }
                $requested = [];
                foreach ( $items as $item ) {
                    $slug = isset( $item['slug'] ) ? sanitize_title( $item['slug'] ) : '';
                    $intro = isset( $item['intro'] ) ? trim( wp_kses_post( $item['intro'] ) ) : '';
                    if ( ! $slug || ! isset( $current_by_slug[ $slug ] ) || ! $intro ) {
                        return new WP_Error( 'invalid_item', 'Ongeldige intro of slug.', [ 'status' => 409 ] );
                    }
                    if ( false === stripos( wp_strip_all_tags( $intro ), 'field service' ) ) {
                        return new WP_Error( 'missing_context', 'Intro mist field service: ' . $slug, [ 'status' => 409 ] );
                    }
                    if ( isset( $requested[ $slug ] ) ) {
                        return new WP_Error( 'duplicate_slug', 'Dubbele slug: ' . $slug, [ 'status' => 409 ] );
                    }
                    $requested[ $slug ] = $intro;
                }
                if ( array_keys( $requested ) !== array_keys( $current_by_slug ) ) {
                    ksort( $requested );
                    ksort( $current_by_slug );
                }
                if ( array_keys( $requested ) !== array_keys( $current_by_slug ) ) {
                    return new WP_Error( 'slug_mismatch', 'Introsets verschillen.', [ 'status' => 409 ] );
                }
                $changed = [];
                foreach ( $requested as $slug => $intro ) {
                    $row = $current_by_slug[ $slug ];
                    if ( $row['intro'] === $intro ) { continue; }
                    $pod = pods( 'beroep_studierichting', (int) $row['id'] );
                    if ( false === $pod->save( 'intro_taxonomy', $intro ) ) {
                        foreach ( array_reverse( $changed ) as $rollback ) {
                            $rollback_pod = pods( 'beroep_studierichting', $rollback['id'] );
                            $rollback_pod->save( 'intro_taxonomy', $rollback['intro'] );
                        }
                        return new WP_Error( 'save_failed', 'Opslaan mislukt; intro’s teruggedraaid.', [ 'status' => 500 ] );
                    }
                    $changed[] = [ 'id' => $row['id'], 'intro' => $row['intro'] ];
                }
                return rest_ensure_response( [ 'changed' => count( $changed ), 'total' => count( $requested ) ] );
            },
        ],
    ] );
} );
""".strip()


def install_snippet(client: Client) -> int:
    result = client.request(
        "POST",
        "/wp-json/code-snippets/v1/snippets",
        {
            "name": "TFSC TEMP Beroepengids studierichting intro 2026-10-19",
            "desc": "Tijdelijk beheerders-endpoint voor bestaand intro_taxonomy-veld; na verificatie verwijderd.",
            "code": intro_snippet(),
            "tags": ["temporary", "content"],
            "scope": "global",
            "active": False,
        },
    )
    snippet_id = result.get("id")
    if not isinstance(snippet_id, int):
        raise PublishError("Geen snippet-ID ontvangen")
    client.request("POST", f"/wp-json/code-snippets/v1/snippets/{snippet_id}/activate")
    return snippet_id


def remove_snippet(client: Client, snippet_id: int) -> None:
    try:
        client.request("POST", f"/wp-json/code-snippets/v1/snippets/{snippet_id}/deactivate")
    finally:
        client.request("DELETE", f"/wp-json/code-snippets/v1/snippets/{snippet_id}")


def update_descriptions(
    client: Client,
    content_by_slug: dict[str, dict[str, Any]],
    terms_by_slug: dict[str, dict[str, Any]],
) -> int:
    changed = 0
    for slug in sorted(content_by_slug):
        intended = content_by_slug[slug]["description"]
        if terms_by_slug[slug].get("description", "") == intended:
            continue
        client.request(
            "POST",
            f"/wp-json/wp/v2/{TAXONOMY}/{int(terms_by_slug[slug]['id'])}",
            {"description": intended},
        )
        changed += 1
    return changed


def restore_descriptions(
    client: Client, terms_before: list[dict[str, Any]]
) -> None:
    for term in terms_before:
        client.request(
            "POST",
            f"/wp-json/wp/v2/{TAXONOMY}/{int(term['id'])}",
            {"description": term.get("description", "")},
        )


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--netrc", required=True)
    parser.add_argument("--node", default="node")
    parser.add_argument("--backup")
    parser.add_argument("--apply", action="store_true")
    args = parser.parse_args()

    content = build_content(args.node)
    client = Client(args.netrc)
    terms_before = fetch_terms(client)
    content_by_slug, terms_by_slug = preflight(content, terms_before)
    audit = {
        "mode": "apply" if args.apply else "audit",
        "taxonomy": TAXONOMY,
        "terms": len(content),
        "unique_headings": sum(item["headingCount"] for item in content),
        "word_count_min": min(item["wordCount"] for item in content),
        "word_count_max": max(item["wordCount"] for item in content),
        "intro_word_count_min": min(len(item["intro"].split()) for item in content),
        "intro_word_count_max": max(len(item["intro"].split()) for item in content),
    }
    if not args.apply:
        print(json.dumps(audit, ensure_ascii=False, indent=2))
        return 0
    if not args.backup:
        raise PublishError("--backup is verplicht bij --apply")

    snippet_id: int | None = None
    intros_before: list[dict[str, Any]] | None = None
    descriptions_changed = 0
    intro_result: dict[str, Any] | None = None
    try:
        snippet_id = install_snippet(client)
        intros_before = client.request("GET", ENDPOINT)
        backup = {
            "created_at": "2026-10-19",
            "taxonomy": TAXONOMY,
            "terms": terms_before,
            "intros": intros_before,
        }
        Path(args.backup).write_text(
            json.dumps(backup, ensure_ascii=False, indent=2) + "\n",
            encoding="utf-8",
        )

        descriptions_changed = update_descriptions(
            client, content_by_slug, terms_by_slug
        )
        intro_result = client.request(
            "POST",
            ENDPOINT,
            {
                "items": [
                    {"slug": item["slug"], "intro": item["intro"]}
                    for item in content
                ]
            },
        )

        terms_after = fetch_terms(client)
        _after_content, after_by_slug = preflight(content, terms_after)
        description_mismatches = [
            slug
            for slug, item in content_by_slug.items()
            if after_by_slug[slug].get("description", "") != item["description"]
        ]
        intros_after = client.request("GET", ENDPOINT)
        intro_by_slug = {row["slug"]: row["intro"] for row in intros_after}
        intro_mismatches = [
            item["slug"]
            for item in content
            if intro_by_slug.get(item["slug"]) != item["intro"]
        ]
        if description_mismatches or intro_mismatches:
            restore_descriptions(client, terms_before)
            if intros_before is not None:
                client.request(
                    "POST",
                    ENDPOINT,
                    {
                        "items": [
                            {"slug": row["slug"], "intro": row["intro"]}
                            for row in intros_before
                        ]
                    },
                )
            raise PublishError(
                f"Eindcontrole mislukt; rollback uitgevoerd. descriptions={description_mismatches}, intro={intro_mismatches}"
            )

        audit.update(
            {
                "descriptions_changed": descriptions_changed,
                "intros_changed": intro_result.get("changed"),
                "descriptions_verified": 23,
                "intros_verified": 23,
                "backup": args.backup,
                "temporary_snippet_removed": True,
            }
        )
    except Exception:
        if descriptions_changed:
            try:
                restore_descriptions(client, terms_before)
            except Exception as rollback_error:
                print(f"WAARSCHUWING descriptionrollback: {rollback_error}", file=sys.stderr)
        raise
    finally:
        if snippet_id is not None:
            remove_snippet(client, snippet_id)

    print(json.dumps(audit, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except (PublishError, KeyError, TypeError, ValueError) as error:
        print(f"FOUT: {error}", file=sys.stderr)
        raise SystemExit(1)
