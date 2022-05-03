# REST API file archiver

## Answers to assignment questions

1. Adding additional archiving method can be done by creating a new class that implements `App\Service\ArchiverMethod`.
2. I've decided to implement the endpoint to accept uploads with _application/x-www-form-urlencoded_ content type and
   responding with actual archive file instead of _JSON_ because it would consume a lot less memory as archiving is done
   using PHP streams on filesystem and not in memory. It would also help with point #3 if the max file size would be
   increased. To support _JSON_ content type it would require to increase PHP capabilities to allow large request bodies
   and increase memory limit. Also, a webserver would require changes to allow large file uploads.
3. To allow 1GB max file size can be done by updating parameter `app.max_file_upload_size` in `config/services.yaml`.
