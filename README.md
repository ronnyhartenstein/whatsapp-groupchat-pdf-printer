
# WhatsApp Chat nach HTML und PDF wandeln

Features:
- Empfänger/Sender auflösen
- Whatsapp Design
- Emoji durch Bilder ersetzen
- fehlende Bilder rot ausgeben ("Bild fehlt [Name]") + Liste nach Abschluss
- Videos einbinden, Thumbnails kommen über Browser selbst
- bei Tagen Timons Alter mit berechnen
- Video-Thumbnails (läd schneller)
- Text bricht in PDF um bei Zeit - via Chrome Headless rendern
- Script um *.db zu adb pullen
- fehlende Medien in JSON schreiben (jedes Mal neu)
- Script für adb pull von fehlender Media von Smartphone

## Herausforderungen
- WhatsApp DBs vom nicht-gerooteten Smartphone herunterbekommen und decrypten
- WhatsApp Design nachbauen, jeder Teilnehmer hat eine Farbe
- Emojis drucken -> als EmojiOne Grafik eingebunden 
  - wkhtmltopdf druckt 2x Platzhalter statt Emoji, hat aber einen encodings-Schalter, nicht geprüft
  - Chrome Headless lässt die Emoji aus, einfach Whitespace
- aus Videos Thumbnail erstellen und mit Play-Button (Single-Div) einbinden
- Timons Alter bzgl. aktuellen Tag relativ berechnen und in Tages-Headline ausgeben

## Installation

- PHP 7
- wget: `brew install wget`
- pdfjam:
  1. `brew cask install basictex`
  2. `sudo tlmgr install pdfjam`
  3. in .zshrc PATH="..:/Library/TeX/Distributions/Programs/texbin"
- Google Chrome

## msgstore.db.crypt12 dekrypten

### automatisch

- man braucht den Schlüssel..
- http://whatcrypt.com/
- "Download Crypt Key Extractor"
- `WhatsApp-Key-DB-Extractor-master/WhatsAppKeyDBExtract.sh` ausführen

```
➜  WhatsApp-Key-DB-Extractor-master ./WhatsAppKeyDBExtract.sh 

=========================================================================
= This script will extract the WhatsApp Key file and DB on Android 4.0+ =
= You DO NOT need root for this to work but you DO need Java installed. =
= If your WhatsApp version is greater than 2.11.431 (most likely), then =
= a legacy version will be installed temporarily in order to get backup =
= permissions. You will NOT lose ANY data and your current version will =
= be restored at the end of the extraction process so try not to panic. =
= Script by: TripCode (Greets to all who visit: XDA Developers Forums). =
= Thanks to: dragomerlin for ABE and to Abinash Bishoyi for being cool. =
=         ###          Version: v4.7 (12/10/2016)          ###          =
=========================================================================


Please connect your Android device with USB Debugging enabled:

error: protocol fault (couldn't read status): Connection reset by peer
* daemon not running. starting it now on port 5037 *
* daemon started successfully *
./WhatsAppKeyDBExtract.sh: line 57: [: -eq: unary operator expected

Downloading legacy WhatsApp 2.11.431 to local folder

  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100 17.4M  100 17.4M    0     0  4759k      0  0:00:03  0:00:03 --:--:-- 4759k

WhatsApp 2.17.254 installed

Backing up WhatsApp 2.17.254
[100%] /data/app/com.whatsapp-1/base.apk
Backup complete

Installing legacy WhatsApp 2.11.431
[100%] /data/local/tmp/LegacyWhatsApp.apk
	pkg: /data/local/tmp/LegacyWhatsApp.apk
Success
Install complete

Now unlock your device and confirm the backup operation...

Please enter your backup password (leave blank for none) and press Enter: 

x apps/com.whatsapp/f/key
x apps/com.whatsapp/db/msgstore.db
x apps/com.whatsapp/db/wa.db
x apps/com.whatsapp/db/axolotl.db
x apps/com.whatsapp/db/chatsettings.db

Saving whatsapp.cryptkey ...
Saving msgstore.db ...
Saving wa.db ...
Saving axolotl.db ...
Saving chatsettings.db ...

Pushing cipher key to: /storage/emulated/legacy/WhatsApp/Databases/.nomedia
[100%] /storage/emulated/legacy/WhatsApp/Databases/.nomedia

Restoring WhatsApp 2.17.254
[100%] /data/local/tmp/base.apk
	pkg: /data/local/tmp/base.apk
Success
Restore complete

Cleaning up temporary files ...
Done

Operation complete

Please press Enter to quit...
```

### manuell via Emulator

Mit Android 7 hat es bei mir nicht mehr funktioniert, das Backup war immer leer. Daher folgender Umweg über den Emulator.

0. in Whatsapp auf eigenen Handy Backup auf Google Drive durchführen/aktualisieren
1. Android Studio 3 installieren
2. SDKs für Android 6 (API 23) installieren via SDK Manager (Android Studio -> Tools -> Android -> SDK Manager)
3. AVD Manager starten, Android 6 Device mit passenden Image (x86) anlegen, starten
4. in Emulator aktuelles Whatsapp installieren
5. Backup aus Google Drive wiederherstellen (Messages reichen, Medien laufen dann im Hintergrund, ist egal)
6. `WhatsAppKeyDBExtract.sh` gegen Emulator laufen lassen (deinstalliert Whatsapp, installiert altes Whatsapp, macht Backup, läd das runter und entpackt es)


## msgstore.db und wa.db einbinden

Source-Dir anlegen und befüllen .. 

```
mkdir source
cd source
cp WhatsApp-Key-DB-Extractor-master/extracted/msgstore.db .
cp WhatsApp-Key-DB-Extractor-master/extracted/wa.db .
```

Wenn OmniCrypt auf dem Handy läuft werden zyklisch die WA-DBs decrypted. 
Die können dann per adb heruntergeladen werden.

```
cd source
adb pull /sdcard/WhatsApp/Databases/msgstore.db
adb pull /sdcard/WhatsApp/Databases/wa.db
```

Oder per Update-Script

```
php update_source.php
```

## Medien herunterladen

- `/sdcard/WhatsApp/Media` nach `source/Media` kopieren

Oder per Update-Script - nach ersten Lauf von `html.php` - wobei die noch fehlenden Medien in `missing_media.json` gelistet werden. 

```
php update_source.php
```

Theoretisch könnte man `rsync` nutzen und alle Medien heruntersyncen - http://ptspts.blogspot.de/2015/03/how-to-use-rsync-over-adb-on-android.html

## HTML bauen & anschauen

- `php html.php`
- `./run_server.sh`
- [http://0.0.0.0:4000/](http://0.0.0.0:4000/) im Browser öffnen

## PDF bauen

- nutzt Chrome Headless, benötigt Chrome ab Version 58
- `./build.sh`
- `pdf/output-offset.pdf` öffnen