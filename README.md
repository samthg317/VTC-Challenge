# Welcome to VTC Challenge - Solved

This challenge is created to test your knowledge of creating Symfony Applications with SPA Frontend build using ReactJS.
In order to start working your solution, please clone this repository and create a copy under your namespace. 
Consider also to change name of your project to make it harder to find your solution by other competitors.

**DO NOT FORK THIS REPOSITORY, THIS WILL ALLOW OTHERS TO COPY YOUR SOLUTION**

## Challenge

Based on this bare-bones application create a service that will allow:
* registration of a user
* confirming account by clicking a link from email. (no need to send actual email, email can be persisted as a file in var/emails directory)
* users to login
* users to create notes
* each note should have fields
  * title
  * content
  * category
  * status (new, todo, done)
* list of notes should have possibility to search notes by text from title/content, a select list for statuses and another select list for categories.

To finish this challenge you have 24h since the moment of receiving this email. Remember to push your last changes before the end of the deadline.
We will be evaluating your solution based on the time of the commit. And remember to send us back link to your solution on GitHub.

Good look.

## Help notes

### Requirements
To run this project you will need:
* Docker: >24
* Docker Compose: >1.29
* NodeJS: >18
* PHP: >7.4

### First steps:

    $ cp .env.dist .env
    $ composer install
    $ yarn install
    $ docker-compose up -d
    $ yarn watch

After running this set of commands, without errors; you should be able to open `http://localhost:81/` and see `Hello World!!!` in the middle of the page.

Alternatively you can run the built-in PHP server included in this project:

    $ php ./bin/composer.phar run-script serve

or (if you have PHP on PATH):

    $ php -S 127.0.0.1:81 -t public

Then open `http://localhost:81/`.

Notes on verification flow:

- When a user registers, the verification email is persisted to `var/emails/` as an `.eml` file.
- Open the file and visit the `Click:` URL to verify the account.

The application supports:
- User registration and account verification (email saved to `var/emails`) 
- Login (email + password)
- Creating notes (title, content, category, status)
- Listing and filtering notes by text, status and category

Good luck — push your final changes before the deadline and send us the link to your repository.
