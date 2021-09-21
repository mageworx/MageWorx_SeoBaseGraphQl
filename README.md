# MageWorx_SeoBaseGraphQl

GraphQL API module for Mageworx [Magento 2 SEO Suite Ultimate](https://www.mageworx.com/magento-2-seo-extension.html) extension. 

## Installation
**1) Copy-to-paste method**
- Download this module and upload it to the `app/code/MageWorx/SeoBaseGraphQl` directory *(create "SeoBaseGraphQl" first if missing)*

**2) Installation using composer (from packagist)**
- Execute the following command: `composer require mageworx/module-seobase-graph-ql`

## How to use
**[SeoBaseGraphQL](https://github.com/mageworx/MageWorx_SeoBaseGraphQl)** module extends existing Output attributes for Product, Category, CmsPage queries and includes:

- mw_canonical_url
- - url
- - code 
- meta_robots
- mw_hreflangs
- - items
- - - url
- - - code

**Note!** In the context of canonical URLs, the code means **store code**. In the context of hreflang URLs, the code means **hreflang code** - for example *x-default*, *en*, *en_US*, etc.

Other attribute is defined according to the guide: https://devdocs.magento.com/guides/v2.4/graphql/queries/products.html#productfilterinput-attributes.

Product, Category, CMS Page queries have the syntax similar to the Magento user guide.

For example, product query has the following syntax:

```
products(
  search: String
  filter: ProductAttributeFilterInput
  pageSize: Int
  currentPage: Int
  sort: ProductAttributeSortInput
): Products
```

**Request:**

```
{
  products(filter: {sku: {eq: "24-WG02"}}) {
    total_count
    items {
      mw_canonical_url {
      	url
        code
      }
      meta_robots
      mw_hreflangs {
        items {
          url
          code
        }
      }
      name
      sku
        }
      }
    }
```

**Response:**

```
{
  "data": {
    "products": {
      "items": [
        {
          "mw_canonical_url": {
            "url": "didi-sport-watch.html",
            "code": "store_code" 
          }          
          "meta_robots": null,
          "mw_hreflangs": {
            "items": [
              {
                "url": "didi-sport-watch.html",
                "code": "en"
              },
              {
                "url": "didi-sport-uhr.html",
                "code": "de"
              }
            ]
          },
          "name": "Didi Sport Watch",
          "sku": "24-WG02"
        }
      ]
    }
  }
}
```
