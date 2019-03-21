# thunix 2.0 website update

This is the code powering the website for thunix, originally founded by hexhaxtron. Since the original site went down, amcclure and ubergeek revived the site, though with only the bare necessities.

These updates are designed to not only beautify the website with updated CSS and fully compliant HTML5 code, but also to automate visitor interaction with site administration and to provide server status information.

Features include:
- Emailing scripts for general inquiries, registration and abuse reporting
- Scripting to automatically present functional user web pages in a menu list
- Scripting to provide server status reports to visitors*

*This requires the monurbox server monitoring shell script, executed through an hourly cron job

To run this website, the server computer requires PHP, with the GD extension (for generating CAPTCHA images,) Sendmail (to send emails from the website, to administration) and monurbox (to generate server status reports).