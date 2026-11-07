#!/usr/bin/env python3
"""Publish an already reviewed ZIP through the site's existing admin access."""
import argparse
import base64
import hashlib
import json
from pathlib import Path
import re
import subprocess
import sys
from urllib.parse import urlsplit


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--site', required=True, help='HTTPS site origin, without /wp-json')
    parser.add_argument('--netrc', required=True, type=Path, help='Existing private authentication file')
    parser.add_argument('--plugin', required=True, help='Existing plugin-folder/main-file.php')
    parser.add_argument('--zip', required=True, type=Path)
    parser.add_argument('--sha256', required=True, help='Checksum recorded when the package was reviewed')
    parser.add_argument('--tested', required=True, help='WordPress version used in the verification')
    parser.add_argument('--notes-file', required=True, type=Path, help='Plain-text user-facing release notes')
    parser.add_argument('--publish', action='store_true', help='Approve this package; otherwise show a local preview')
    args = parser.parse_args()
    origin = urlsplit(args.site)
    if origin.scheme != 'https' or not origin.hostname or origin.username or origin.password or origin.query or origin.fragment or origin.path not in ('', '/'):
        parser.error('--site must be an HTTPS origin without credentials or extra paths')
    if not re.fullmatch(r'[a-z0-9_-]+/[a-z0-9_-]+\.php', args.plugin):
        parser.error('Invalid plugin identity')
    if not re.fullmatch(r'[a-f0-9]{64}', args.sha256):
        parser.error('Invalid reviewed SHA-256')
    if args.zip.stat().st_size > 2097152:
        parser.error('ZIP exceeds the provider limit')
    archive = args.zip.read_bytes()
    if hashlib.sha256(archive).hexdigest() != args.sha256:
        parser.error('ZIP differs from the reviewed checksum')
    notes = args.notes_file.read_text(encoding='utf-8')
    if len(notes.encode('utf-8')) > 8000:
        parser.error('Release notes exceed the provider limit')
    preview = {'plugin': args.plugin, 'sha256': args.sha256, 'zip_bytes': len(archive), 'tested': args.tested, 'notes': notes, 'published': False}
    if not args.publish:
        print(json.dumps(preview, ensure_ascii=False, indent=2))
        return
    payload = {'plugin': args.plugin, 'sha256': args.sha256, 'archive': base64.b64encode(archive).decode('ascii'), 'tested': args.tested, 'notes': notes}
    result = subprocess.run(['curl', '--silent', '--show-error', '--max-time', '60', '--netrc-file', str(args.netrc.resolve()), '--request', 'POST', '--header', 'Content-Type: application/json', '--data-binary', '@-', '--write-out', '\n%{http_code}', args.site.rstrip('/') + '/wp-json/tfsc-updates/v1/releases'], input=json.dumps(payload), capture_output=True, text=True)
    if result.returncode:
        raise RuntimeError('Publication request failed; inspect site connectivity and authentication')
    body, status = result.stdout.rsplit('\n', 1)
    response = json.loads(body)
    if not 200 <= int(status) < 300:
        raise RuntimeError('Publication refused: HTTP ' + status + ' ' + str(response.get('code', 'unknown_error')))
    print(json.dumps({key: response[key] for key in ('id', 'state', 'plugin', 'version', 'sha256', 'tested')}, indent=2))


if __name__ == '__main__':
    try:
        main()
    except (OSError, ValueError, RuntimeError) as error:
        print(str(error), file=sys.stderr)
        sys.exit(1)
