# "Aggregation API in Ibexa DXP" developer webinar demo 

Source code of demos presented in "Aggregation API in Ibexa DXP" developer webinar. https://www.ibexa.co/forms/webinar-aggregation-api-in-ibexa-dxp.

## Installation

1.  Please follow the Ibexa DXP installation instructions: https://doc.ibexa.co/en/latest/getting_started/install_ez_platform/
2.  Switch search engine to Solr or ElasticSearch. Please remember to rebuild search index: `php bin/console ezplatform:reindex`
3.  Generate demo data `php bin/console ezplatform:aggregation-demo:create-data`

## Table of Contents

1.  [Sandbox command](https://github.com/adamwojs/ezplatform-aggregations-demo/blob/main/src/Command/SandboxCommand.php) `(php bin/console ezplatform:aggregation-demo:sandbox)`
2.  [Repository metrics](https://github.com/adamwojs/ezplatform-aggregations-demo/blob/main/src/Command/RepositoryMetricsCommand.php) `(php bin/console ezplatform:aggregation-demo:metrics)`
3.  [Product reviews page](https://github.com/adamwojs/ezplatform-aggregations-demo/blob/main/src/Controller/ProductReviewController.php) (http://127.0.0.1:8000/example-product)
4.  [Tag cloud](https://github.com/adamwojs/ezplatform-aggregations-demo/blob/main/src/Controller/TagCloudController.php) (http://127.0.0.1:8000/tag-cloud)
5.  [Faceted search](https://github.com/adamwojs/ezplatform-aggregations-demo/blob/main/src/Controller/SearchController.php) (http://127.0.0.1:8000/facet-search)
