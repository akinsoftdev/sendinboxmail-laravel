<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SendInboxMail API URL
    |--------------------------------------------------------------------------
    |
    | The endpoint URL for SendInboxMail mail service API. This is the URL where
    | all email sending requests will be dispatched to.
    |
    */
    'url' => env('SENDINBOXMAIL_URL', 'https://notify.sendinboxmail.com/manage/send.php'),

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Your SendInboxMail API authentication key. This key is used to authenticate
    | your requests with the SendInboxMail API service.
    |
    */
    'apikey' => env('SENDINBOXMAIL_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | News ID
    |--------------------------------------------------------------------------
    |
    | The news/campaign identifier for your emails. This ID is used to track
    | and categorize your email campaigns within SendInboxMail.
    |
    */
    'news_id' => env('SENDINBOXMAIL_NEWS_ID'),

    /*
    |--------------------------------------------------------------------------
    | Sender ID
    |--------------------------------------------------------------------------
    |
    | The sender identifier used for outgoing emails. This determines which
    | sender profile will be used when dispatching emails.
    |
    */
    'sender_id' => env('SENDINBOXMAIL_SENDER_ID'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The maximum number of seconds to wait for a response from the SendInboxMail
    | API. Increase this value if you experience timeout issues.
    |
    */
    'timeout' => env('SENDINBOXMAIL_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Times
    |--------------------------------------------------------------------------
    |
    | Number of times to retry failed requests before throwing an exception.
    | Each retry will have an exponential backoff delay.
    |
    */
    'retry_times' => env('SENDINBOXMAIL_RETRY_TIMES', 3),
];
