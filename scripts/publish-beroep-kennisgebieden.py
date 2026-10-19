#!/usr/bin/env python3
"""Controleer en publiceer archiefcontent voor beroep_kennisgebied."""

from __future__ import annotations

import importlib.util
import sys
from pathlib import Path
from typing import Any


BASE_SCRIPT = Path(__file__).with_name("publish-beroep-bedrijfssectoren.py")
SPEC = importlib.util.spec_from_file_location("beroep_archive_publisher", BASE_SCRIPT)
if SPEC is None or SPEC.loader is None:
    raise RuntimeError("Basispublisher kon niet worden geladen")
publisher = importlib.util.module_from_spec(SPEC)
sys.modules[SPEC.name] = publisher
SPEC.loader.exec_module(publisher)

publisher.TAXONOMY = "beroep_kennisgebied"
publisher.ENDPOINT = "/wp-json/tfsc-admin/v1/beroep-kennisgebied-content-20261019"
publisher.EXPECTED_COUNT = 25
publisher.SNIPPET_NAME = "TFSC TEMP Beroepengids kennisgebieden content 2026-10-19"


def build_content(node: str) -> list[dict[str, Any]]:
    result = publisher.subprocess.run(
        [node, "scripts/build-beroep-kennisgebieden.mjs"],
        capture_output=True,
        text=True,
    )
    if result.returncode != 0:
        raise publisher.PublishError(f"Contentbuild mislukt: {result.stderr.strip()}")
    items = publisher.json.loads(result.stdout)
    if not isinstance(items, list) or len(items) != publisher.EXPECTED_COUNT:
        raise publisher.PublishError("Contentbuild bevat niet exact 25 kennisgebieden")
    return items


def metadata_snippet() -> str:
    snippet = publisher._business_sector_metadata_snippet()
    replacements = {
        "tfsc_20261019_business_sector_snapshot": "tfsc_20261019_knowledge_area_snapshot",
        "beroep_bedrijfssector": "beroep_kennisgebied",
        "beroep-bedrijfssector-content-20261019": "beroep-kennisgebied-content-20261019",
        "count( $items ) !== 33 || count( $current ) !== 33": "count( $items ) !== 25 || count( $current ) !== 25",
        "Verwacht exact 33 sectoren.": "Verwacht exact 25 kennisgebieden.",
        "'Bedrijfssector '": "'Kennisgebied '",
    }
    for old, new in replacements.items():
        snippet = snippet.replace(old, new)
    return snippet


publisher._business_sector_metadata_snippet = publisher.metadata_snippet
publisher.build_content = build_content
publisher.metadata_snippet = metadata_snippet


if __name__ == "__main__":
    try:
        raise SystemExit(publisher.main())
    except (publisher.PublishError, KeyError, TypeError, ValueError) as error:
        print(f"FOUT: {error}", file=sys.stderr)
        raise SystemExit(1)
