#!/bin/bash

SERVER="0.0.0.0:4000"

echo "Test Server $SERVER"
wget --spider http://$SERVER --quiet
if [[ $? -ne 0 ]]; then
  echo "ERROR: Server $SERVER is not running! Use './run_server.sh' to serve the pages."
  exit
fi

echo "--------------"
echo "generiere HTML .."
php html.php

echo "--------------"
echo "generiere PDF .."
cd pdf
# https://developers.google.com/web/updates/2017/04/headless-chrome
/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --headless --disable-gpu --print-to-pdf http://$SERVER/

echo "--------------"
echo "fuege Binderand in PDF hinzu .."

# pdfjam installieren:
# 1. brew cask install basictex
# 2. sudo tlmgr install pdfjam
# 3. in .zshrc PATH="..:/Library/TeX/Distributions/Programs/texbin"

# pdfjam: http://www2.warwick.ac.uk/fac/sci/statistics/staff/academic-research/firth/software/pdfjam/#using
pdfjam --twoside output.pdf --offset '0.8cm 0cm' --suffix 'offset'

echo "Done."