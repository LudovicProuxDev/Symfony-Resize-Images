# Symfony-Resize-Images

This is a project to resize images with [Tinify](https://tinypng.com/).

Go to Tinify and create your API key to resize images.

Get the dependencies and follow instructions:

1. Create database in MySQL
```bash
symfony console doctrine:database:create
```

2. Execute migration
```bash
symfony console doctrine:migrations:migrate
```

3. Get your API key from Tinify and use it into the *FileUploader* Service, and replace the text into the **API_KEY** constant
```php
// src/Service/FileUploader.php
<?php

namespace App\Service;

// ...

class FileUploader
{
    private const API_KEY = 'YOUR_API_KEY';
    // ...
}

```


3. Run the app
```bash
symfony server:start
```

Now, we can upload images to resize them.