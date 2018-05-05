Exercise for creating extension attributes and custom indexer

Adds new extension attribute to catalog_product: boolean is_fragile.
If true, product is fragile and may require special treatment during shipping (e.g. sticker "fragile" on the parcel).
Then, indexer is created to store this value in separate flat table.