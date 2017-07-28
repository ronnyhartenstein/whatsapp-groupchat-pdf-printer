
# WhatsApp Chat nach HTML und PDF wandeln

## msgstore.db.crypt12 dekrypten

- man braucht den Schlüssel..
- http://whatcrypt.com/
- "Download Crypt Key Extractor"
- `WhatsApp-Key-DB-Extractor-master/WhatsAppKeyDBExtract.sh` ausführen

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