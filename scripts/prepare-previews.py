#!/usr/bin/env python3
"""Prepare development fixtures from a local osTicket 1.18.4 tree; no downloads."""
from pathlib import Path
import argparse
import shutil
parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument('osticket_source', type=Path)
args = parser.parse_args()
root = Path(__file__).resolve().parents[1]
files = ['css/select2.min.css', 'css/typeahead.css', 'css/osticket.css', 'css/font-awesome.min.css', 'css/redactor.css', 'css/thread.css', 'js/jquery-ui-1.13.2.custom.min.js', 'js/jquery-3.7.0.min.js', 'js/redactor.min.js', 'assets/font/fontawesome-webfont.woff', 'assets/default/css/theme.css', 'scp/css/scp.css', 'scp/css/dropdown.css', 'scp/css/login.css', 'scp/js/jb.overflow.menu.js']
missing = [name for name in files if not (args.osticket_source / name).is_file()]
if missing:
    parser.error('Missing source files: ' + ', '.join(missing))
for name in files:
    dest = root / 'preview/legacy' / name
    dest.parent.mkdir(parents=True, exist_ok=True)
    shutil.copy2(args.osticket_source / name, dest)
print(f'Prepared {len(files)} local preview assets. Do not commit these third-party copies.')
