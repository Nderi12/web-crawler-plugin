# Problem to be solved:
The problem this WordPress plugin aims to solve is to provide website administrators with a convenient way to scan and review all internal links within their website. By doing so, administrators can identify any broken or problematic links and take appropriate actions to enhance their website's SEO rankings.

# Technical spec to solve the problem:
* To solve this problem, the plugin performs automatic crawls of the website's home page at regular intervals, such as every hour using the wordpress wp_ajax hook.
* It scans all the internal links on the homepage and creates a sitemap page that displays these links in an organized manner. 
* The sitemap page is accessible from the WordPress admin dashboard, allowing administrators to easily review the homepage links.
* The web crawler is accessible by the admin on the settings page. The menu item is displayed as "Crawler".
* The web crawler has three fundamental function: Starting the crawler, Viewing the retrived links and Resetting the Crawler.
* Triggered by an AJAX call, starting the web crawler follows the following steps:
    1. Deletes previous crawl's link data.
    2. Deletes the previous sitemap.html file from the storage folder.
    3. Retrieves internal links from the home page using the DomDocument library in PHP.
    4. Stores the link data in the database as JSON using set_transient.
    5. Creates a new sitemap.html file with the updated link data in the storage folder.
    6. Saves the HTML content of the home page as homepage.html in the storage folder.
    7. Sets up a scheduled event in WordPress to execute these steps every hour.
    8. Displays the list of links to the admin to view.
* Triggered by an AJAX call, the admin is able to view the links which are retrieved and decoded link data from the database. This is then displayed as a list of links on the DOM, and is only visible when there is existing data to show.
* Resetting the crawler is triggered by an AJAX call. It resets all crawler data by performing the following operations: 
    1. Deleting link data from the database.
    3. Removing the cron schedule event.
    3. Deleting the sitemap.html file from the storage folder.


# Technical decisions and reasoning:
**1. Automatic Crawling:**
The decision to perform automatic crawls of the home page every hour was made to ensure that the sitemap page stays up-to-date. By regularly scanning the home page, any changes or additions to the internal links can be captured and reflected in the sitemap page without manual intervention.

**2. Displaying Links on a Sitemap Page:**
Creating a dedicated sitemap page to display the internal links provides a centralized and easily accessible location for administrators to review all the links. It allows them to quickly identify any broken or problematic links and take necessary actions to improve SEO rankings.

**3. WordPress Integration:**
Building the functionality as a WordPress plugin enables seamless integration with the WordPress admin dashboard. Administrators can access the sitemap page directly from their familiar WordPress environment, reducing the need to switch between different tools or platforms.

# How the code works and why:
The code for the WordPress plugin utilizes WordPress hooks and functions to implement the desired functionality. Here's an overview of how it works:

**1. Crawling the Home Page:**
The plugin uses WordPress's scheduling feature to schedule a recurring event that triggers the crawling process. The event is set to occur every hour. When the event is triggered, the plugin retrieves the home page URL and fetches its content.

**2. Parsing Internal Links:**
Once the home page content is obtained, the plugin parses it to extract all the internal links. It uses HTML parsing techniques, such as regular expressions or a dedicated HTML parser library, to identify and extract the links.

**3. Creating the Sitemap Page:**
After extracting the internal links, the plugin generates a sitemap page within WordPress. It either creates a new page or updates an existing one, depending on the configuration. The sitemap page is populated with the extracted links, formatted in an organized manner, such as a list or a table.

**4. Admin Access and SEO Enhancement:**
The sitemap page is accessible from the WordPress admin dashboard, typically under a dedicated menu item. Administrators can visit the sitemap page to review all the internal links and identify any issues. By addressing broken or problematic links, administrators can improve the website's SEO rankings, as search engines prioritize websites with clean and functional link structures.

# Achieving the admin's desired outcome:
* The plugin achieves the desired outcome of the administrator by providing an automated and convenient solution for reviewing internal links. 
* It saves the administrator's time by performing regular crawls of the home page, generating a sitemap page, and displaying the links in an easily accessible format.
* The administrator can navigate to the sitemap page within the WordPress admin dashboard, review the links, and take appropriate actions to enhance the website's SEO rankings, ultimately improving its visibility and search engine performance.