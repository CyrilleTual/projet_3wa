SELECT  products.ProductRef , products.teaser, products.description, products.picture, products.status, 
categories.categoryName,
items.itemRef, items.pack, items.price, items.stock, items.status,
vat.rate, vat.name
FROM `products` 
INNER JOIN categories 
ON products.id_category = categories.id_category
INNER JOIN items
ON products.id_product = items.id_product
INNER JOIN vat
ON items.id_vat = vat.id_vat


WHERE products.id_product = 1