php-timer
===================

PHP timer for long-time operations

Installation
------------
Run
```code
composer require demi/php-timelog
```

Usage
-----
For any console action:
```php
$bigData = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

$allCount = count($bigData);
$timer = new \demi\timelog\TimeLog($allCount);
$timer->showStart();
foreach ($bigData as $item) {
    // some handling start...
    sleep(rand(1, 2));
    // some handling finish...

    $timer->handled++;
    $timer->showStatus(3); // 3 - how often show status message
}
$timer->showFinish();
```

Output:
```bash
Starting handling: 10 items
Handled:  3/10  Remaining: 00:00:09     Speed: 0.74/sec
Handled:  6/10  Remaining: 00:00:05     Speed: 0.74/sec
Handled:  9/10  Remaining: 00:00:01     Speed: 0.75/sec

Finished after 00:00:14 Handled: 10 items
```
