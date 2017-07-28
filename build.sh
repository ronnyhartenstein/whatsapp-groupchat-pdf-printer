#!/bin/bash

SERVER="0.0.0.0:4000"

echo "Test Server $SERVER"
wget --spider http://$SERVER --quiet
if [[ $? -ne 0 ]]; then
  echo "ERROR: Server $SERVER is not running! Use './run_server.sh' to serve the pages."
  exit
fi

echo "--------------\ngeneriere HTML .."
php html.php

echo "--------------\ngeneriere PDFs .."
cd html
for html in *.html; do
  if [[ "$html" == "print.html" || "$html" == "footer.html" ]]; then
    continue
  fi
  pdf=../pdf/$(basename "$html" .html).pdf
  echo "--> " $html $pdf
  # https://wkhtmltopdf.org/usage/wkhtmltopdf.txt
  wkhtmltopdf \
    --disable-plugins \
    --disable-javascript \
    --disable-forms \
    --lowquality \
    --no-print-media-type \
    --images \
    --image-dpi 300 \
    --image-quality 85 \
    --outline \
    --page-size A4 \
    --footer-html footer.html \
    --footer-spacing 5 \
    --viewport-size 800 \
    --load-error-handling ignore \
    --margin-bottom 15 \
    http://$SERVER/$html $pdf
done


# pdfjam installieren:
# 1. brew cask install basictex
# 2. sudo tlmgr install pdfjam
# 3. in .zshrc PATH="..:/Library/TeX/Distributions/Programs/texbin"

cd ../pdf
echo "fuege Binderand hinzu .."
# pdfjam: http://www2.warwick.ac.uk/fac/sci/statistics/staff/academic-research/firth/software/pdfjam/#using
pdfjam --twoside index.pdf --offset '0.8cm 0cm' --suffix 'offset'

echo "Done."