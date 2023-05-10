# What inside

The project uses the library https://github.com/php-telegram-bot/core

**Telegram bot commands:**

/start - start of quiz
/getScore - get a total score of user

**Submit a new quiz**

```
POST http://symf.localhost/api/quiz
Accept: application/json
Content-type: application/json

{
"task": "A question/math task",
"correct_answer": "correct answer",
"type": "theoretic/math",
"incorrect_answers": [
"one",
"two",
"three"
]
}
```
**Create a report**

```
GET http://symf.localhost/api/create-report
Accept: application/json
Content-type: application/json

###

GET http://symf.localhost/api/get-report-status/{filename}
Accept: application/json
Content-type: application/json
```

**Get a report**

http://symf.localhost/get-report/{filename}