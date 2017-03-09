## Notes

##### To install this project clone the repo and run

<code>
composer install
</code>

##### To populate the database import the file _samknows_mysqldump.sql_ located in the migrations folder to your local MySQL instance.

##### In terms of code improvements:
* The Models could be refactored to use an ORM.
* Lack of code comments due to time constraints.
* A caching mechanism (Redis for example) could be implemented to improve performance.
* Implement validation.
* Implement error codes.
* Implement security (if necessary).
* Implement API throttling.