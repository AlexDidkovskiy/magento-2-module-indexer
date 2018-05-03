Practice of creating custom indexers

Adds new extension attribute to catalog_product: boolean fragile.
If true, product is fragile and may require special treatment during shipping (e.g. sticker "fragile" on the parcel).
Then, indexer is created to store this value in separate flat table.