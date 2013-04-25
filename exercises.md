# Configuration

1. Install Redis in 4 easy steps (from http://redis.io/download):

		wget http://redis.googlecode.com/files/redis-2.6.12.tar.gz
		tar xzf redis-2.6.12.tar.gz
		cd redis-2.6.12
		make
		
2. Start redis (in another terminal):

        src/redis-server
        
3. Checkout the repository

        git clone git://github.com/paugay/redis-workshop-welovephp.git

4. Install composer and run it: 

		curl -sS https://getcomposer.org/installer | php
		./composer.phar install

5. Edit _config.php_ with your mysql settings
6. Load the given _items.sql_ schema:

		mysql dbname < items.sql

# 1 - Showing latests items

We have a web application where we want to display the lastest 20 items created by our users.

We will usually proceed as follows:

	SELECT id, title FROM items ORDER BY ts LIMIT 20

__Task 1.1__: Create a "live cache" where the new items that are created are being saved into this new cache.
Show the latest N created items and limit the amount of items that we want to store into the list.

__Task 1.2__: Implement the case where we delete an item from the application.

__Task 1.3__: Can you think in any other improvement?

# 2 - Counters

We want to keep a counter with the number of times the users have "clicked" into a particular item.

We could add a new column into our table, but it will not be optimal in cases where we have lots of 
concurrent users using our application. We need something better in order to "block - read - increment - write".

__Task 2.1__: Create the needed structure in order to count the number of clicks globally, per week, per day and per
hour. 

__Task 2.2__: Show a list of the popular items in the last hour, day and week.
