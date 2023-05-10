
Hello, I would like to present my project to your attention. This is a Symfony-based Telegram bot that sends quizzes on the topic chosen by the user.
There is an option to add new quizzes via an API, and also to receive an active chats report by hour, percentage of correct answers, information about completed and not completed quizzes.

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
POST http://symf.localhost/api/create-report
Accept: application/json
Content-type: application/json
```

**Get report status**

```
GET http://symf.localhost/api/get-report-status/{filename}
Accept: application/json
Content-type: application/json
```

**Get a report**

http://symf.localhost/get-report/{filename}