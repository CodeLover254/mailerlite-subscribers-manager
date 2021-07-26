## Mailerlite Subscribers Manager
This repository contains the code base to the Mailerlite API Integration project. Follow the steps below
in order to successfully interact with the application.

## Environment Requirements
<ul>
    <li>PHP 7.4</li>
    <li>MySQL 5.x Onwards</li>
</ul>

## Steps to successfully run the application
<ul>
    <li>Clone the project from this repository.</li>
    <li>Open the terminal and navigate to <b>mailerlite-subscibers-manager</b>> folder.</li>
    <li>Run <i>composer install</i> command to install required dependencies</li>
    <li>For the database sql file, navigate to database/scripts folder and use the file
        to create and populate the database using your favourite MySQL database tool.</li>
    <li>Copy .env.example to .env and change the DB_DATABASE, DB_PASSWORD, DB_USERNAME to credentials matching your database setup</li>
    <li><b>IMPORTANT!</b> Add your Mailerlite API Key to the env file at the key named MAILERLITE_TEST_API_KEY. Without this key, tests will not run successfully.</li>
    <li>Run the <i>php artisan key:generate</i> command to generate an application key.</li>
    <li>Run the <i>php artisan serve</i> command and navigate to the url provided to start using the application.</li>
</ul>
