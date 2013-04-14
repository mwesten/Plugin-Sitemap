Plugin-Sitemap
=============

Dynamic Sitemap plugin for Statamic 1.5
(for Statamic 1.4 use the `statamic-1_4` branch)

# Installation
## Copy or clone the files to their destination
Download the project and add the contents of archive to the `_add-ons/sitemap` folder.

Or clone this project on your system:

    cd webfolder/_add-ons
    git clone git://github.com/mwesten/Plugin-Sitemap.git sitemap
    

## Copy the layout and template files
Copy the files from the `_add-ons/sitemap/layouts` folder to your `_themes/themename/layouts` folder.

Copy the files from the `_add-ons/sitemap/templates` folder to your `_themes/themename/templates` folder.

## Copy the sitemap-page
Copy the file `sitemap.md` from the `_add-ons/sitemap/content` folder to your `_content` folder.

The sitemap will then be available by calling `http://example.com/sitemap`.

You can also rename the file. If you rename the file to  `sitemap.xml.md` ti will be available as `http://example.com/sitemap.xml`.


# Usage
By default all non-hidden folders/pages/entries will be scanned and included in the sitemap.
It adds the URL of the folder/page/entry, adds the last modification time(**lastmod**) and based on how long the last change is made, sets the change frequency(**changefreq**) accordingly and adds the weight of the page(**priority**).
The priority can be set between 0 and 1 and is by default 0.5.
If you want to change the priority for a page, you can add the variable `priority: 0.6` to the YAML prematter of the markdown file.



# Disclaimer
I've 'written' this plugin for my own use. It comes without any guarantee, so your mileage may vary in using it. If you find bugs or have great additions you'd like to share, use github to fork the project and share your improvements by initiating pull requests.
