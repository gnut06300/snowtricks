# SnowTricks
![alt text](https://snowtricks.iwebprod.fr/pictures/logo_1.png)

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/65f19c9712d4497297811a62fdfd4361)](https://www.codacy.com/gh/gnut06300/snowtricks/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=gnut06300/snowtricks&amp;utm_campaign=Badge_Grade)

## INSTALLATION
### __Download or clone__
Download zip files or clone the project repository with github ([see GitHub documentation](https://docs.github.com/en/github/creating-cloning-and-archiving-repositories/cloning-a-repository)).

### __Install the project__
1.  If needed, install __Composer__ by following [the official instructions](https://getcomposer.org/download/).
2.  In your cmd, go to the directory where you want to install the project and install dependencies with composer:
```
$ cd some\directory
$ composer install
```
Dependencies should be installed in your project (check _vendor_ directory).

### __Create the database__
1. If the database does not exist, create it with the following command in the project directory:
```
$ php bin/console doctrine:database:create
```
2. Create database structure thanks to migrations:
```
$ php bin/console doctrine:migrations:migrate
```

3. Install fixtures to have first contents and your admin account (update the fixtures files before if needed):
```
$ php bin/console doctrine:fixtures:load
```
Your database should be updated with contents.  

#### __You are ready to use your app!__  
Loggued in as a registered user (with verified email address), you can manage all tricks and your own messages.  
As an simple visitor, you can just see the content.  