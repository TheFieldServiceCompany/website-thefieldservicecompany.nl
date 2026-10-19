#!/usr/bin/env python3
"""Synchroniseer vijf bestaande vacaturetaxonomieën naar Beroepengids.

De termaanmaak gebruikt de standaard WordPress REST API en verstuurt bewust
alleen ``name`` en ``slug``. De al bestaande Pods-relatievelden worden via een
tijdelijk, beheerders-only REST-endpoint gevuld en daarna gecontroleerd.

Zonder ``--apply`` voert dit script uitsluitend controles uit.
"""

from __future__ import annotations

import argparse
import html
import json
import subprocess
import sys
from dataclasses import dataclass
from typing import Any
from urllib.parse import urlencode


BASE_URL = "https://thefieldservicecompany.nl"
RESOLVE = "thefieldservicecompany.nl:443:45.82.188.97"


@dataclass(frozen=True)
class Mapping:
    source: str
    target: str
    relationship: str
    expected_field_id: int


MAPPINGS = (
    Mapping("bedrijfssector", "beroep_bedrijfssector", "relationship_bedrijfssector", 24716),
    Mapping("functie-inhoud", "beroep_functie-inhoud", "relationship_functie-inhoud", 24489),
    Mapping("functienaam", "beroep_functienaam", "relationship_beroep_functienaam", 24514),
    Mapping("kennisgebied", "beroep_kennisgebied", "relationship_kennisgebied", 24720),
    Mapping("werkgebied", "beroep_werkgebied", "relationship_werkgebied", 24726),
)


class RestError(RuntimeError):
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
            raise RestError(
                f"{method} {path} mislukt ({result.returncode}): "
                f"{result.stderr.strip()} {result.stdout.strip()}"
            )
        try:
            return json.loads(result.stdout)
        except json.JSONDecodeError as error:
            raise RestError(f"Ongeldige JSON van {method} {path}") from error


def fetch_terms(client: Client, taxonomy: str) -> list[dict[str, Any]]:
    query = urlencode(
        {
            "context": "edit",
            "hide_empty": "false",
            "per_page": 100,
            "orderby": "id",
            "order": "asc",
        }
    )
    response = client.request("GET", f"/wp-json/wp/v2/{taxonomy}?{query}")
    if not isinstance(response, list):
        raise RestError(f"Onverwacht antwoord voor taxonomy {taxonomy}")
    return response


def index_by_slug(terms: list[dict[str, Any]], taxonomy: str) -> dict[str, dict[str, Any]]:
    indexed: dict[str, dict[str, Any]] = {}
    for term in terms:
        slug = term.get("slug")
        if not isinstance(slug, str) or not slug:
            raise RestError(f"Term zonder geldige slug in {taxonomy}")
        if slug in indexed:
            raise RestError(f"Dubbele slug {slug!r} in {taxonomy}")
        if int(term.get("parent", 0)) != 0:
            raise RestError(
                f"Hiërarchie aangetroffen in {taxonomy}: {slug}. Niet automatisch gewijzigd."
            )
        indexed[slug] = term
    return indexed


def clean_name(value: Any) -> str:
    return html.unescape(str(value)).strip()


def verify_schema(client: Client) -> None:
    schema = client.request("GET", "/wp-json/tfsc/v1/schema")
    serialized = json.dumps(schema, ensure_ascii=False)
    for mapping in MAPPINGS:
        required = (
            mapping.target,
            mapping.relationship,
            mapping.source,
            str(mapping.expected_field_id),
        )
        missing = [value for value in required if value not in serialized]
        if missing:
            raise RestError(
                f"Schema-controle mislukt voor {mapping.target}; ontbrekend: {missing}"
            )


def inventory(client: Client) -> tuple[dict[str, Any], int]:
    report: dict[str, Any] = {}
    missing_total = 0
    for mapping in MAPPINGS:
        source_terms = fetch_terms(client, mapping.source)
        target_terms = fetch_terms(client, mapping.target)
        source = index_by_slug(source_terms, mapping.source)
        target = index_by_slug(target_terms, mapping.target)

        extras = sorted(set(target) - set(source))
        if extras:
            raise RestError(
                f"Onverwachte bestaande doeltermen in {mapping.target}: {extras}. "
                "Er is niets gewijzigd."
            )

        name_conflicts = []
        for slug in sorted(set(source) & set(target)):
            if clean_name(source[slug].get("name")) != clean_name(target[slug].get("name")):
                name_conflicts.append(slug)

        missing = sorted(set(source) - set(target))
        missing_total += len(missing)
        report[mapping.target] = {
            "source_taxonomy": mapping.source,
            "source_count": len(source),
            "target_count": len(target),
            "missing_count": len(missing),
            "missing_slugs": missing,
            "existing_count": len(set(source) & set(target)),
            "name_mismatch_count": len(name_conflicts),
            "name_mismatch_slugs": name_conflicts,
        }
    return report, missing_total


def sync_terms(
    client: Client,
) -> tuple[list[tuple[str, int, str]], list[tuple[str, int, str, str]]]:
    created: list[tuple[str, int, str]] = []
    renamed: list[tuple[str, int, str, str]] = []
    try:
        for mapping in MAPPINGS:
            source = index_by_slug(fetch_terms(client, mapping.source), mapping.source)
            target = index_by_slug(fetch_terms(client, mapping.target), mapping.target)
            for slug in sorted(set(source) & set(target)):
                source_name = clean_name(source[slug]["name"])
                target_name = clean_name(target[slug]["name"])
                if source_name == target_name:
                    continue
                client.request(
                    "POST",
                    f"/wp-json/wp/v2/{mapping.target}/{int(target[slug]['id'])}",
                    {"name": source_name},
                )
                renamed.append(
                    (
                        mapping.target,
                        int(target[slug]["id"]),
                        slug,
                        target_name,
                    )
                )
            for slug in sorted(set(source) - set(target)):
                new_term = client.request(
                    "POST",
                    f"/wp-json/wp/v2/{mapping.target}",
                    {
                        "name": clean_name(source[slug]["name"]),
                        "slug": slug,
                    },
                )
                created.append((mapping.target, int(new_term["id"]), slug))
    except Exception:
        for taxonomy, term_id, _slug in reversed(created):
            try:
                client.request(
                    "DELETE",
                    f"/wp-json/wp/v2/{taxonomy}/{term_id}?force=true",
                )
            except Exception as rollback_error:
                print(f"WAARSCHUWING: rollback mislukt voor {taxonomy}/{term_id}: {rollback_error}", file=sys.stderr)
        for taxonomy, term_id, _slug, previous_name in reversed(renamed):
            try:
                client.request(
                    "POST",
                    f"/wp-json/wp/v2/{taxonomy}/{term_id}",
                    {"name": previous_name},
                )
            except Exception as rollback_error:
                print(f"WAARSCHUWING: naamrollback mislukt voor {taxonomy}/{term_id}: {rollback_error}", file=sys.stderr)
        raise
    return created, renamed


def rollback_terms(
    client: Client,
    created: list[tuple[str, int, str]],
    renamed: list[tuple[str, int, str, str]],
) -> None:
    errors: list[str] = []
    for taxonomy, term_id, _slug in reversed(created):
        try:
            client.request("DELETE", f"/wp-json/wp/v2/{taxonomy}/{term_id}?force=true")
        except Exception as error:
            errors.append(f"{taxonomy}/{term_id}: {error}")
    for taxonomy, term_id, _slug, previous_name in reversed(renamed):
        try:
            client.request(
                "POST",
                f"/wp-json/wp/v2/{taxonomy}/{term_id}",
                {"name": previous_name},
            )
        except Exception as error:
            errors.append(f"{taxonomy}/{term_id}: {error}")
    if errors:
        raise RestError("Termrollback niet volledig gelukt: " + "; ".join(errors))


def relationship_snippet() -> str:
    config = {
        mapping.target: {
            "source": mapping.source,
            "field": mapping.relationship,
        }
        for mapping in MAPPINGS
    }
    config_php = "[\n" + ",\n".join(
        "    " + json.dumps(target) + " => [ 'source' => "
        + json.dumps(values["source"]) + ", 'field' => "
        + json.dumps(values["field"]) + " ]"
        for target, values in config.items()
    ) + "\n]"

    return f"""
function tfsc_20261019_beroep_sync_config() {{
    return {config_php};
}}

function tfsc_20261019_beroep_relation_ids( $value ) {{
    $ids = [];
    $walk = function( $item ) use ( &$ids, &$walk ) {{
        if ( $item instanceof WP_Term ) {{
            $ids[] = (int) $item->term_id;
            return;
        }}
        if ( is_numeric( $item ) ) {{
            $ids[] = (int) $item;
            return;
        }}
        if ( is_array( $item ) ) {{
            foreach ( [ 'term_id', 'ID', 'id' ] as $key ) {{
                if ( isset( $item[ $key ] ) && is_numeric( $item[ $key ] ) ) {{
                    $ids[] = (int) $item[ $key ];
                    return;
                }}
            }}
            foreach ( $item as $child ) {{
                $walk( $child );
            }}
        }}
    }};
    $walk( $value );
    $ids = array_values( array_unique( array_filter( $ids ) ) );
    sort( $ids );
    return $ids;
}}

function tfsc_20261019_beroep_sync_snapshot() {{
    if ( ! function_exists( 'pods' ) ) {{
        return new WP_Error( 'pods_missing', 'Pods is niet beschikbaar.', [ 'status' => 500 ] );
    }}
    $rows = [];
    foreach ( tfsc_20261019_beroep_sync_config() as $target_taxonomy => $settings ) {{
        if ( ! taxonomy_exists( $target_taxonomy ) || ! taxonomy_exists( $settings['source'] ) ) {{
            return new WP_Error( 'taxonomy_missing', 'Taxonomy ontbreekt: ' . $target_taxonomy, [ 'status' => 500 ] );
        }}
        $sources = get_terms( [ 'taxonomy' => $settings['source'], 'hide_empty' => false ] );
        $targets = get_terms( [ 'taxonomy' => $target_taxonomy, 'hide_empty' => false ] );
        if ( is_wp_error( $sources ) || is_wp_error( $targets ) ) {{
            return new WP_Error( 'terms_failed', 'Termen konden niet worden geladen.', [ 'status' => 500 ] );
        }}
        $source_by_slug = [];
        foreach ( $sources as $source ) {{
            $source_by_slug[ $source->slug ] = $source;
        }}
        $target_by_slug = [];
        foreach ( $targets as $target ) {{
            $target_by_slug[ $target->slug ] = $target;
        }}
        if ( array_keys( $source_by_slug ) !== array_keys( $target_by_slug ) ) {{
            ksort( $source_by_slug );
            ksort( $target_by_slug );
        }}
        if ( array_keys( $source_by_slug ) !== array_keys( $target_by_slug ) ) {{
            return new WP_Error( 'term_sets_differ', 'Bron- en doelsets verschillen voor ' . $target_taxonomy, [ 'status' => 409 ] );
        }}
        foreach ( $source_by_slug as $slug => $source ) {{
            $target = $target_by_slug[ $slug ];
            if ( $source->name !== $target->name ) {{
                return new WP_Error( 'term_names_differ', 'Termnamen verschillen voor ' . $target_taxonomy . '/' . $slug, [ 'status' => 409 ] );
            }}
            $pod = pods( $target_taxonomy, (int) $target->term_id );
            if ( ! $pod || ! $pod->exists() ) {{
                return new WP_Error( 'pod_missing', 'Pod-term ontbreekt: ' . $target_taxonomy . '/' . $slug, [ 'status' => 500 ] );
            }}
            $current = tfsc_20261019_beroep_relation_ids( $pod->field( $settings['field'] ) );
            $rows[] = [
                'target_taxonomy' => $target_taxonomy,
                'source_taxonomy' => $settings['source'],
                'field' => $settings['field'],
                'slug' => $slug,
                'target_term_id' => (int) $target->term_id,
                'expected_source_term_id' => (int) $source->term_id,
                'current_source_term_ids' => $current,
                'matches' => [ (int) $source->term_id ] === $current,
            ];
        }}
    }}
    return $rows;
}}

add_action( 'rest_api_init', function() {{
    register_rest_route( 'tfsc-admin/v1', '/beroep-taxonomy-sync-20261019', [
        [
            'methods' => 'GET',
            'permission_callback' => function() {{ return current_user_can( 'manage_options' ); }},
            'callback' => function() {{
                $rows = tfsc_20261019_beroep_sync_snapshot();
                if ( is_wp_error( $rows ) ) {{ return $rows; }}
                return rest_ensure_response( [
                    'total' => count( $rows ),
                    'matching' => count( array_filter( $rows, function( $row ) {{ return $row['matches']; }} ) ),
                    'rows' => $rows,
                ] );
            }},
        ],
        [
            'methods' => 'POST',
            'permission_callback' => function() {{ return current_user_can( 'manage_options' ); }},
            'callback' => function() {{
                $rows = tfsc_20261019_beroep_sync_snapshot();
                if ( is_wp_error( $rows ) ) {{ return $rows; }}
                foreach ( $rows as $row ) {{
                    if ( ! empty( $row['current_source_term_ids'] ) && ! $row['matches'] ) {{
                        return new WP_Error( 'relation_conflict', 'Bestaande afwijkende relatie bij ' . $row['target_taxonomy'] . '/' . $row['slug'], [ 'status' => 409 ] );
                    }}
                }}
                $changed = [];
                foreach ( $rows as $row ) {{
                    if ( $row['matches'] ) {{ continue; }}
                    $pod = pods( $row['target_taxonomy'], $row['target_term_id'] );
                    $previous = $pod->field( $row['field'] );
                    if ( false === $pod->save( $row['field'], [ $row['expected_source_term_id'] ] ) ) {{
                        foreach ( array_reverse( $changed ) as $rollback ) {{
                            $rollback_pod = pods( $rollback['target_taxonomy'], $rollback['target_term_id'] );
                            $rollback_pod->save( $rollback['field'], $rollback['previous'] );
                        }}
                        return new WP_Error( 'save_failed', 'Relatie opslaan mislukt bij ' . $row['target_taxonomy'] . '/' . $row['slug'] . '; wijzigingen teruggedraaid.', [ 'status' => 500 ] );
                    }}
                    $changed[] = [
                        'target_taxonomy' => $row['target_taxonomy'],
                        'target_term_id' => $row['target_term_id'],
                        'field' => $row['field'],
                        'previous' => $previous,
                    ];
                }}
                $verified = tfsc_20261019_beroep_sync_snapshot();
                if ( is_wp_error( $verified ) ) {{ return $verified; }}
                $matching = count( array_filter( $verified, function( $row ) {{ return $row['matches']; }} ) );
                if ( $matching !== count( $verified ) ) {{
                    return new WP_Error( 'verify_failed', 'Niet alle relaties zijn na opslag correct.', [ 'status' => 500 ] );
                }}
                return rest_ensure_response( [ 'changed' => count( $changed ), 'total' => count( $verified ), 'matching' => $matching ] );
            }},
        ],
    ] );
}} );
""".strip()


def install_snippet(client: Client) -> int:
    response = client.request(
        "POST",
        "/wp-json/code-snippets/v1/snippets",
        {
            "name": "TFSC TEMP Beroepengids taxonomierelaties 2026-10-19",
            "desc": "Tijdelijk beheerders-endpoint voor exact vijf bestaande relatievelden; na verificatie verwijderd.",
            "code": relationship_snippet(),
            "tags": ["temporary", "migration"],
            "scope": "global",
            "active": False,
        },
    )
    snippet_id = response.get("id")
    if not isinstance(snippet_id, int):
        raise RestError(f"Snippet-ID ontbreekt in antwoord: {response}")
    client.request("POST", f"/wp-json/code-snippets/v1/snippets/{snippet_id}/activate")
    return snippet_id


def remove_snippet(client: Client, snippet_id: int) -> None:
    try:
        client.request("POST", f"/wp-json/code-snippets/v1/snippets/{snippet_id}/deactivate")
    finally:
        client.request("DELETE", f"/wp-json/code-snippets/v1/snippets/{snippet_id}")


def verify_exact_terms(client: Client) -> dict[str, int]:
    counts: dict[str, int] = {}
    for mapping in MAPPINGS:
        source = index_by_slug(fetch_terms(client, mapping.source), mapping.source)
        target = index_by_slug(fetch_terms(client, mapping.target), mapping.target)
        if set(source) != set(target):
            raise RestError(f"Slugs verschillen na synchronisatie: {mapping.target}")
        for slug in source:
            if clean_name(source[slug]["name"]) != clean_name(target[slug]["name"]):
                raise RestError(f"Naam verschilt na synchronisatie: {mapping.target}/{slug}")
        counts[mapping.target] = len(target)
    return counts


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--netrc", required=True, help="Pad naar het bestaande netrc-bestand")
    parser.add_argument("--apply", action="store_true", help="Maak ontbrekende termen en relaties aan")
    args = parser.parse_args()

    client = Client(args.netrc)
    verify_schema(client)
    before, missing_total = inventory(client)
    print(json.dumps({"mode": "apply" if args.apply else "audit", "before": before}, ensure_ascii=False, indent=2))
    if not args.apply:
        return 0

    created, renamed = sync_terms(client)
    counts = verify_exact_terms(client)

    snippet_id: int | None = None
    relationship_result: dict[str, Any] | None = None
    verification: dict[str, Any] | None = None
    try:
        snippet_id = install_snippet(client)
        relationship_result = client.request(
            "POST", "/wp-json/tfsc-admin/v1/beroep-taxonomy-sync-20261019"
        )
        verification = client.request(
            "GET", "/wp-json/tfsc-admin/v1/beroep-taxonomy-sync-20261019"
        )
        if verification.get("matching") != verification.get("total"):
            raise RestError("Relatieverificatie rapporteert niet-matchende records")
    except Exception:
        rollback_terms(client, created, renamed)
        raise
    finally:
        if snippet_id is not None:
            remove_snippet(client, snippet_id)

    print(
        json.dumps(
            {
                "requested_missing_before": missing_total,
                "created_terms": len(created),
                "renamed_existing_terms": len(renamed),
                "created_by_taxonomy": {
                    taxonomy: sum(1 for item in created if item[0] == taxonomy)
                    for taxonomy in counts
                },
                "final_counts": counts,
                "relationships": relationship_result,
                "verified_relationships": {
                    "total": verification.get("total") if verification else None,
                    "matching": verification.get("matching") if verification else None,
                },
                "temporary_snippet_removed": True,
            },
            ensure_ascii=False,
            indent=2,
        )
    )
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except (RestError, KeyError, TypeError, ValueError) as error:
        print(f"FOUT: {error}", file=sys.stderr)
        raise SystemExit(1)
