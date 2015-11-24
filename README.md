# financialProject

e-Banking Web site written in Php + MySql Database

COMP7370 Information Processing in Financial Services Final Project

Installation Guide:

Download the entire project using git:

$ git clone https://github.com/t04st3r/financialProject.git

Move the content of financialProject folder inside your public Web Server folder 
(normally should be something like /var/www or /var/www/html or htdoc)

Dump of the database can be found inside the db folder, 
to import it on MySQL server enter login into MySQL: 

$ mysql -u root -p

Create an empty database schema called financial:

$ mysql> CREATE DATABASE financial;

You can now create a new user and grant few privileges on the database:

$ mysql> GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, 
CREATE TEMPORARY TABLES, LOCK TABLES ON financial.* 
TO 'yourusername'@'localhost' IDENTIFIED BY 'yourpassword';

Or grant ALL privileges to EVERY database schema:

$ mysql> GRANT ALL PRIVILEGES ON *.* TO 'yourusername'@'localhost' 
IDENTIFIED BY 'yourpassword' WITH GRANT OPTION;

Once done you can import the database dump (file database_dump.sql inside
db folder) giving this command:

$ mysql -u <username previously created> -p financial < database_dump.sql

to set the database name, user, password, host on the php code you should modify 
the file db.php on its very first rows, the actual data to eventually modify is 
the following:

class db {

    private $schema = 'financial';
    private $user = 'fin_user';
    private $password = 'Fin::Account::314';
    private $host = 'localhost';  

.....

if you are not familiar with shell command you can download and install GUI 
tools such as: 

phpmyadmin (https://www.phpmyadmin.net/)

or:

mysql workbench (https://dev.mysql.com/downloads/workbench/)
