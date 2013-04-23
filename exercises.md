# Showing latests items (live cache)

We have a web application where we want to display the lastest 20 items created by our users.

We will usually proceed as follows:

	SELECT id, title FROM items ORDER BY ts LIMIT 20

__Task 1__: Create a "live cache" where the new items that are created are being saved into this new cache.

__Task 2__: Show the latest N created items.

__Task 3__: Limit the amount of items that we want to store into the list.

__Task 4__: Implement the case where we delete an item from the application.


# Counters

We want to keep a counter with the number of times the users have "clicked" into a particular item.

We could add a new column into our table, but it will not be optimal in cases where we have lots of 
concurrent users using our application. We need something better in order to "block - read - increment - write".

__Task 1__: Create the needed structure in order to count the number of clicks globally, per week, per day and per
hour. 

__Task 2__: Show a list of the popular items in the last hour, day and week.
