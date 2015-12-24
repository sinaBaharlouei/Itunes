-- 1) Showing all pop music in order of price

SELECT * FROM song  WHERE id IN (SELECT song_id FROM song_genre WHERE genre='pop') ORDER BY price;

-- 2)

-- 3)
start transaction;
INSERT INTO `transaction` (user_id, payment_number) VALUES (1,1);
INSERT INTO `order` (user_id, payment_number, song_id) VALUES (1,1,2);
INSERT INTO `order` (user_id, payment_number, song_id) VALUES (1,1,3);
INSERT INTO `order` (user_id, payment_number, song_id) VALUES (1,1,4);
commit;