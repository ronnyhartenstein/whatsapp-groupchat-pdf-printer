#!/bin/bash

echo -e "\nBitte Android device anstecken mit aktiviertem USB Debugging:\n"
adb kill-server
adb start-server
adb wait-for-device

cd source
adb pull /sdcard/WhatsApp/Databases/msgstore.db
adb pull /sdcard/WhatsApp/Databases/wa.db
cd -

cd "source/WhatsApp/Media/WhatsApp Video"
adb pull ""/sdcard/WhatsApp/Media/WhatsApp Video/*"
