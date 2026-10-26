#!/usr/bin/env python3
"""Build a reproducible installable ZIP without test files or local settings."""
from pathlib import Path
from zipfile import ZipFile, ZipInfo, ZIP_DEFLATED

root = Path(__file__).resolve().parent
target = root.parent / "dist" / "tfsc-media-filters-1.0.0.zip"
target.parent.mkdir(exist_ok=True)
files = ["tfsc-media-filters.php", "uninstall.php", "README.md", "readme.txt",
         "assets/media-filters.js", "assets/media-filters.css"]
with ZipFile(target, "w", compression=ZIP_DEFLATED, compresslevel=9) as archive:
    for name in sorted(files):
        info = ZipInfo("tfsc-media-filters/" + name, date_time=(2026, 10, 26, 12, 0, 0))
        info.compress_type = ZIP_DEFLATED
        info.external_attr = 0o100644 << 16
        archive.writestr(info, (root / name).read_bytes())
print(target)
