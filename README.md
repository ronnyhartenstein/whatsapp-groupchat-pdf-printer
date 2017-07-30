
# WhatsApp Chat nach HTML und PDF wandeln

TODO:
- [ ] Emoji durch Bilder ersetzen
- [ ] Empfänger/Sender auflösen

## msgstore.db.crypt12 dekrypten

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

## msgstore.db herunterladen

- Source-Dir anlegen und befüllen .. 
```
mkdir source
cd source
adb pull /sdcard/WhatsApp/Databases/msgstore.db
```

## Medien herunterladen

- `/sdcard/WhatsApp/Media` nach `source/Media` kopieren

## HTML bauen & anschauen

- `php html.php`
- `./run_server.sh`
- [http://0.0.0.0:4000/](http://0.0.0.0:4000/) im Browser öffnen

## PDF bauen

- `./build.sh`
- `pdf/index-offset.pdf` öffnen