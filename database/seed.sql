-- Maato demo data. Run after schema.sql.
-- Seed logins are listed in README.md.
USE `suman-ecom`;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO roles (id, slug, name, description) VALUES
(1,'admin','Administrator','Full access, including people, settings and the audit log.'),
(2,'staff','Staff','Runs the catalogue, orders and reviews.'),
(3,'customer','Customer','Shops on the storefront.');

INSERT INTO users (id, role_id, name, email, phone, password_hash, avatar_path, is_blocked, last_login_at, created_at, updated_at) VALUES
(1,1,'Prakriti Joshi','admin@maato.test','9801234567','$2y$12$BhNxAthB3fsTDVZUJmcqWO5Plj3Xi/hqgPJR0KFvZJbynAJleFJQq',NULL,0,DATE_SUB(NOW(), INTERVAL 30 HOUR),DATE_SUB(NOW(), INTERVAL 2880 HOUR),DATE_SUB(NOW(), INTERVAL 2880 HOUR)),
(2,2,'Nabin Maharjan','staff@maato.test','9812345678','$2y$12$Ku7T1M97icEHomX06tIZhOhYO15FOg9DJjNFe137foxVWMXCdM4s2',NULL,0,DATE_SUB(NOW(), INTERVAL 30 HOUR),DATE_SUB(NOW(), INTERVAL 2400 HOUR),DATE_SUB(NOW(), INTERVAL 2400 HOUR)),
(3,3,'Aarati Gurung','aarati@example.com','9841122334','$2y$12$Bi.dv8cdse9.0uHUgEeV9eEgc93TyPmnPGXKikUtIBebcyT3nXS4.',NULL,0,DATE_SUB(NOW(), INTERVAL 30 HOUR),DATE_SUB(NOW(), INTERVAL 1800 HOUR),DATE_SUB(NOW(), INTERVAL 1800 HOUR)),
(4,3,'Bikash Thapa','bikash@example.com','9851098765','$2y$12$WySljuT1fpr.gFb1/J2dz.EjxrWTsTYBIJ4hf4PSaD2/nNMSok9NG',NULL,0,DATE_SUB(NOW(), INTERVAL 30 HOUR),DATE_SUB(NOW(), INTERVAL 1200 HOUR),DATE_SUB(NOW(), INTERVAL 1200 HOUR)),
(5,3,'Sneha Rai','sneha@example.com','9860456789','$2y$12$OjH4PK7CQ7wuSzdn8dqGS.0Tz4DJP339LTC8JP8dyXTwqyUY9ue6a',NULL,0,DATE_SUB(NOW(), INTERVAL 30 HOUR),DATE_SUB(NOW(), INTERVAL 96 HOUR),DATE_SUB(NOW(), INTERVAL 96 HOUR));

INSERT INTO addresses (id, user_id, label, recipient_name, phone, line1, line2, city, district, province, postal_code, is_default) VALUES
(1,3,'Home','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',1),
(2,3,'Studio','Aarati Gurung','9841122334','2nd floor, Thamel Marg 7','Near Kathmandu Guest House','Kathmandu','Kathmandu','Bagmati','44600',0),
(3,4,'Home','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',1),
(4,5,'Home','Sneha Rai','9860456789','Suryabinayak 3, Sirutar',NULL,'Bhaktapur','Bhaktapur','Bagmati','44800',1);

INSERT INTO brands (id, name, slug, origin, description, is_active) VALUES
(1,'Maato Studio','maato-studio','Jhamsikhel, Lalitpur','Our in-house design studio. We sketch, prototype and test every piece at home before it goes to a maker.',1),
(2,'Thimi Clayworks','thimi-clayworks','Thimi, Bhaktapur','A family pottery of eleven people working on four wheels and a wood-fired kiln behind the Balkumari temple.',1),
(3,'Patan Lightworks','patan-lightworks','Patan, Lalitpur','Metalsmiths who spent three generations making temple bells before turning their lathes to lamps.',1),
(4,'Bet & Baans Collective','bet-baans','Chitwan','Thirty-two weavers, mostly women, working cane and bamboo harvested on a rotating cycle.',1),
(5,'Kaath Workshop','kaath-workshop','Pokhara','A small joinery by Phewa Lake that builds in solid oak and walnut with traditional joints.',1),
(6,'Gorkha Loom','gorkha-loom','Gorkha','Handloom weavers working in linen, cotton and highland wool on pit and frame looms.',1),
(7,'Himal Apothecary','himal-apothecary','Jumla','Small-batch oils, soaps and candles made with wild-harvested herbs from the Karnali.',1);

INSERT INTO categories (id, parent_id, name, slug, tagline, description, image_path, sort_order, is_active) VALUES
(1,NULL,'Tableware & Ceramics','tableware-ceramics','Wheel-thrown in Thimi, fired twice, made to be used every day.','Mugs, plates and vessels from a single family pottery in Thimi. Stoneware that goes in the dishwasher and still looks handmade ten years later.','categories/tableware.jpg',1,1),
(2,NULL,'Lighting','lighting','Light for long evenings, not showrooms.','Pendants, floor lamps and bedside lights spun, bent and woven by hand in Patan and Chitwan. Warm, low and easy to live under.','categories/lighting.jpg',2,1),
(3,NULL,'Furniture','furniture','Solid wood, honest joints, built to be passed on.','Chairs, stools, tables and shelves from a joinery in Pokhara. Solid oak and walnut, finished with oil so scratches can be sanded out instead of hidden.','categories/furniture.jpg',3,1),
(4,NULL,'Bed & Textiles','bed-textiles','Linen and wool that get softer every wash.','Bedding, throws, cushions and rugs from handloom weavers in Gorkha. Natural fibres, plant-friendly dyes, sizes that fit Nepali beds.','categories/bed-textiles.jpg',4,1),
(5,NULL,'Apothecary','apothecary','Small-batch scent and soap from the Karnali.','Candles, oils and soaps from Jumla, made with wild-harvested herbs. No synthetic fragrance, no palm oil.','categories/apothecary.jpg',5,1),
(6,1,'Drinkware','drinkware','Cups for chiya, coffee and everything between.',NULL,NULL,1,1),
(7,1,'Vases & Vessels','vases-vessels','Pieces that look finished empty.',NULL,NULL,2,1),
(8,3,'Seating','seating','Chairs and stools worth sitting in.',NULL,NULL,1,1),
(9,3,'Tables & Shelving','tables-shelving','Surfaces for the things you keep.',NULL,NULL,2,1);

INSERT INTO products (id, category_id, brand_id, name, slug, short_description, description, details, price, sale_price, sale_starts_at, sale_ends_at, status, is_featured, size_guide, meta_title, meta_description, created_at, updated_at) VALUES
(1,6,2,'Chiya Morning Mug','chiya-morning-mug','A wide, heavy-bottomed 350 ml mug for slow first cups.','We designed the Chiya mug around the way tea is actually drunk at home: both hands wrapped around it, refilled twice before anyone leaves for work. It is thrown on the wheel in Thimi, trimmed by hand and glazed in a satin finish that feels like river stone.

The handle fits three fingers comfortably, and the wide base won''t tip when someone bumps the table. Every piece is slightly different; small shifts in glaze and shape are how you can tell a person made it.','Stoneware, lead-free satin glaze
350 ml · 9 cm tall · 8.5 cm across
Dishwasher and microwave safe
Made in Thimi, Bhaktapur',1450,NULL,NULL,NULL,'published',1,NULL,'Chiya Morning Mug | Maato','A wide, heavy-bottomed 350 ml mug for slow first cups.',DATE_SUB(NOW(), INTERVAL 1920 HOUR),DATE_SUB(NOW(), INTERVAL 1920 HOUR)),
(2,6,2,'Dhunge Speckled Tumblers, Set of 4','dhunge-speckled-tumblers-set-of-4','Handleless cups in iron-flecked clay, for water, chiya or a short evening pour.','Dhunge means "of stone", and that is how these feel in the hand. The clay is dug near Thimi and carries tiny flecks of iron that bloom into dark speckles in the kiln. Raw at the foot, glazed inside, and sized to stack neatly on an open shelf.

Sold as four, each one a little different from the next.','Speckled stoneware, food-safe glaze inside
250 ml each · 8 cm tall
Dishwasher safe; hand-washing keeps the raw foot looking its best
Set of 4',2800,NULL,NULL,NULL,'published',1,NULL,'Dhunge Speckled Tumblers, Set of 4 | Maato','Handleless cups in iron-flecked clay, for water, chiya or a short evening pour.',DATE_SUB(NOW(), INTERVAL 1536 HOUR),DATE_SUB(NOW(), INTERVAL 1536 HOUR)),
(3,7,2,'Speckled Bottle Vase','speckled-bottle-vase','A narrow-necked bottle for a single branch of marigold or a few dried grasses.','Tall, quiet and very slightly lopsided in the right way. The bottle vase is glazed in an oatmeal white that lets the speckle of the clay show through. It looks finished with nothing in it, and better with one stem.

Fully watertight, so fresh flowers are fine.','Stoneware, matte oatmeal glaze
26 cm tall · 9 cm wide
Watertight · wipe clean
Made in Thimi, Bhaktapur',3200,NULL,NULL,NULL,'published',0,NULL,'Speckled Bottle Vase | Maato','A narrow-necked bottle for a single branch of marigold or a few dried grasses.',DATE_SUB(NOW(), INTERVAL 1392 HOUR),DATE_SUB(NOW(), INTERVAL 1392 HOUR)),
(4,7,2,'Ash Glazed Vessel Trio','ash-glazed-vessel-trio','Three slender vessels in a smoky wood-ash glaze, made to be grouped.','Three heights, one glaze. Wood-ash glaze pools darker at the shoulders and thins to almost-grey at the rim, so together the trio reads like a small landscape on a sideboard or windowsill.

The set is fired side by side in the same kiln load, so the tones match. Use them with dried seed heads, or leave them empty.','Stoneware, wood-ash glaze
Heights 18 cm, 24 cm and 31 cm
Not watertight; best with dried stems
Set of 3',6500,5400,DATE_SUB(NOW(), INTERVAL 120 HOUR),DATE_ADD(NOW(), INTERVAL 12 DAY),'published',1,NULL,'Ash Glazed Vessel Trio | Maato','Three slender vessels in a smoky wood-ash glaze, made to be grouped.',DATE_SUB(NOW(), INTERVAL 960 HOUR),DATE_SUB(NOW(), INTERVAL 960 HOUR)),
(5,7,2,'Kumal Amphora Vase','kumal-amphora-vase','A full-bodied floor vase with the rounded profile of Kumal river pottery.','The Kumal community has shaped clay along Nepal''s rivers for generations, and this vase borrows their generous, rounded profile. It is coil-built, smoothed and burnished before a chalk-white glaze goes on.

The medium sits well on a console. The large is happiest on the floor beside a sofa, or in the corner that has always needed something.','Coil-built stoneware, chalk glaze
Medium 34 cm tall · Large 48 cm tall
Watertight
Made in small batches; expect gentle variation',8900,NULL,NULL,NULL,'published',0,NULL,'Kumal Amphora Vase | Maato','A full-bodied floor vase with the rounded profile of Kumal river pottery.',DATE_SUB(NOW(), INTERVAL 792 HOUR),DATE_SUB(NOW(), INTERVAL 792 HOUR)),
(6,1,2,'Nilo Stoneware Dinner Plates, Set of 4','nilo-stoneware-dinner-plates','Everyday 27 cm plates in a pooled, lake-blue glaze.','Nilo is the blue of high lakes in late autumn. The glaze breaks lighter at the rim and pools darker in the centre, so dal bhat looks good on it, and so does toast.

Rims are raised just enough to keep things on the plate, and they stack flat without wobbling.','Stoneware, reactive glaze
27 cm across · 2.5 cm deep
Dishwasher and microwave safe
Set of 4',5600,NULL,NULL,NULL,'published',1,NULL,'Nilo Stoneware Dinner Plates, Set of 4 | Maato','Everyday 27 cm plates in a pooled, lake-blue glaze.',DATE_SUB(NOW(), INTERVAL 1680 HOUR),DATE_SUB(NOW(), INTERVAL 1680 HOUR)),
(7,7,1,'Twin Bud Vases with Oak Tray','twin-bud-vases-with-oak-tray','Two porcelain bud vases on a cloud-shaped oak tray.','A small still life for a desk or bedside table. Two porcelain bud vases, one tall and one short, sit on a tray cut from offcuts of the oak we use for our furniture.

We are still testing the tray finish, so this one is not on sale yet.','Porcelain vases, oiled oak tray
Tray 28 × 14 cm · vases 12 and 17 cm tall
Vases are watertight',3900,NULL,NULL,NULL,'draft',0,NULL,'Twin Bud Vases with Oak Tray | Maato','Two porcelain bud vases on a cloud-shaped oak tray.',DATE_SUB(NOW(), INTERVAL 48 HOUR),DATE_SUB(NOW(), INTERVAL 48 HOUR)),
(8,2,3,'Atelier Enamel Pendant','atelier-enamel-pendant','A spun-steel dome with a warm enamel inside, for kitchen islands and dining tables.','Spun by hand in a Patan workshop that made temple bells for three generations. The shade is a single piece of steel, enamelled inside to throw soft, warm light down onto the table rather than into anyone''s eyes.

It comes with 1.5 m of fabric-covered cord, so you can set the drop height when it is installed.','Spun steel, enamel finish
32 cm diameter · 22 cm tall
E27 bulb, up to 60 W (LED recommended)
1.5 m fabric cord and ceiling canopy included
Installation by a qualified electrician',7800,NULL,NULL,NULL,'published',1,NULL,'Atelier Enamel Pendant | Maato','A spun-steel dome with a warm enamel inside, for kitchen islands and dining tables.',DATE_SUB(NOW(), INTERVAL 2112 HOUR),DATE_SUB(NOW(), INTERVAL 2112 HOUR)),
(9,2,3,'Studio Task Floor Lamp','studio-task-floor-lamp','An adjustable reading lamp with a tilting head and a weighted iron base.','Put it behind a reading chair or next to a desk. The head tilts and swivels so light lands on the page, and the base is cast from recycled iron so it stays put on rugs.

A foot switch on the cord means you never have to reach behind the chair.','Powder-coated steel, cast iron base
145 cm tall · base 26 cm across
E27 bulb, up to 40 W
Foot switch on a 2 m cord',12500,NULL,NULL,NULL,'published',0,NULL,'Studio Task Floor Lamp | Maato','An adjustable reading lamp with a tilting head and a weighted iron base.',DATE_SUB(NOW(), INTERVAL 1440 HOUR),DATE_SUB(NOW(), INTERVAL 1440 HOUR)),
(10,2,3,'Halo Arc Floor Lamp','halo-arc-floor-lamp','A sweeping arc that brings overhead light to the sofa without new ceiling wiring.','Rented flats rarely have a light where you actually sit. The Halo fixes that: the base stands beside the sofa and the shade hangs over it, almost two metres out. The arm is brushed steel and the base is a block of honed stone.

It arrives in three parts and goes together in about ten minutes.','Brushed steel arm, honed stone base
195 cm tall · 170 cm reach
E27 bulb, up to 60 W
Inline dimmer on the cord
Assembly about 10 minutes',18900,15900,DATE_SUB(NOW(), INTERVAL 72 HOUR),DATE_ADD(NOW(), INTERVAL 9 DAY),'published',0,NULL,'Halo Arc Floor Lamp | Maato','A sweeping arc that brings overhead light to the sofa without new ceiling wiring.',DATE_SUB(NOW(), INTERVAL 1080 HOUR),DATE_SUB(NOW(), INTERVAL 1080 HOUR)),
(11,2,4,'Bet Rattan Pendant','bet-rattan-pendant','A woven cane pendant that throws a soft lattice of light across the ceiling.','Woven in Chitwan from cane harvested on a rotating cycle, so the plants have time to recover. Switched on, it scatters a gentle pattern across walls and ceiling; switched off, it is a basket of warm honey tones.

The weave is close enough to take the glare off a bare bulb.','Hand-woven cane over a steel ring
Small 40 cm Ø · Large 55 cm Ø
E27 LED bulb, up to 40 W equivalent
1.2 m cord and canopy included
Dust with a soft, dry brush',6200,NULL,NULL,NULL,'published',1,NULL,'Bet Rattan Pendant | Maato','A woven cane pendant that throws a soft lattice of light across the ceiling.',DATE_SUB(NOW(), INTERVAL 480 HOUR),DATE_SUB(NOW(), INTERVAL 480 HOUR)),
(12,2,3,'Brass Bedside Lamp','brass-bedside-lamp','A small brass lamp with a clear filament bulb, sized for narrow bedside tables.','Most bedside tables are smaller than lamp makers think. This one has a 12 cm footprint, a soft dimming switch on the cord, and a brass body that darkens slowly to a warm, lived-in brown.

The bulb is included, and it is the warm 2200 K kind that makes a room feel like evening.','Solid brass, unlacquered
34 cm tall · 12 cm base
E14 filament bulb included (2200 K)
Dimmer on a 1.8 m cord',5400,NULL,NULL,NULL,'published',0,NULL,'Brass Bedside Lamp | Maato','A small brass lamp with a clear filament bulb, sized for narrow bedside tables.',DATE_SUB(NOW(), INTERVAL 1248 HOUR),DATE_SUB(NOW(), INTERVAL 1248 HOUR)),
(13,2,3,'Orbit Ring Chandelier','orbit-ring-chandelier','Two hand-bent steel rings with a continuous LED band, for high-ceilinged dining rooms.','Two rings, bent by hand around a wooden form, hang at slightly different heights so the light layers rather than glares. The LED band inside each ring is fully dimmable and rated for 30,000 hours.

Designed for ceilings of 2.8 m or more.','Powder-coated steel, integrated LED
Rings 60 cm and 45 cm Ø
3000 K warm white, 2,400 lumens total
Drop adjustable to 150 cm
Installation by a qualified electrician',24500,NULL,NULL,NULL,'published',0,NULL,'Orbit Ring Chandelier | Maato','Two hand-bent steel rings with a continuous LED band, for high-ceilinged dining rooms.',DATE_SUB(NOW(), INTERVAL 288 HOUR),DATE_SUB(NOW(), INTERVAL 288 HOUR)),
(14,8,5,'Bhaktapur Oak Bar Stool','bhaktapur-oak-bar-stool','A four-legged oak stool with a carved finger slot for carrying it one-handed.','Built in solid oak with mortise-and-tenon joints and no metal brackets. The seat has a shallow dish carved into it and a slot in the middle, so you can pick it up with one hand while the other holds a cup.

Choose counter height for kitchen islands, bar height for higher tables.','Solid oak, hard-wax oil finish
Counter 65 cm or bar 75 cm seat height
Seat 34 × 28 cm
Supports 120 kg',8900,NULL,NULL,NULL,'published',0,'furniture','Bhaktapur Oak Bar Stool | Maato','A four-legged oak stool with a carved finger slot for carrying it one-handed.',DATE_SUB(NOW(), INTERVAL 1800 HOUR),DATE_SUB(NOW(), INTERVAL 1800 HOUR)),
(15,8,1,'Shell Side Chair','shell-side-chair','A moulded shell seat on oak dowel legs. Light enough to move with one hand.','Our take on a mid-century classic, made for Kathmandu kitchens where chairs are pulled out, moved to the balcony and carried back again. The shell is recycled polypropylene, and the legs are solid oak with a steel cross-brace.

It wipes clean, and it stacks two high in a pinch.','Recycled polypropylene shell, oak legs, steel brace
81 cm tall · seat height 45 cm
46 × 53 cm footprint
Indoor use',7200,NULL,NULL,NULL,'published',0,'furniture','Shell Side Chair | Maato','A moulded shell seat on oak dowel legs. Light enough to move with one hand.',DATE_SUB(NOW(), INTERVAL 1320 HOUR),DATE_SUB(NOW(), INTERVAL 1320 HOUR)),
(16,8,5,'Forest Lounge Chair','forest-lounge-chair','A low walnut lounge chair upholstered in deep green wool.','Low enough to sink into, firm enough that you can get out again. The frame is solid walnut with exposed through-joints, and the cushions are covered in a dense highland wool woven in Gorkha.

The cushions zip off for cleaning, and the frame can be re-oiled at home.','Solid walnut frame, wool upholstery
72 cm tall · 70 cm wide · 78 cm deep
Seat height 38 cm
Removable cushion covers',38500,NULL,NULL,NULL,'published',1,'furniture','Forest Lounge Chair | Maato','A low walnut lounge chair upholstered in deep green wool.',DATE_SUB(NOW(), INTERVAL 720 HOUR),DATE_SUB(NOW(), INTERVAL 720 HOUR)),
(17,8,5,'Marigold Reading Armchair','marigold-reading-armchair','A high-backed armchair in marigold cotton, with arms wide enough for a cup.','Named for the garlands strung across doorways at Tihar. The back is tall enough to rest your head, and the arms are flat and wide, so a cup of tea can sit there without a coaster.

The frame is oak; the cover is a tight cotton twill that stands up to everyday use.','Oak frame, cotton twill upholstery
92 cm tall · 74 cm wide · 80 cm deep
Seat height 44 cm
Spot clean only',32000,27500,DATE_SUB(NOW(), INTERVAL 144 HOUR),DATE_ADD(NOW(), INTERVAL 14 DAY),'published',1,'furniture','Marigold Reading Armchair | Maato','A high-backed armchair in marigold cotton, with arms wide enough for a cup.',DATE_SUB(NOW(), INTERVAL 624 HOUR),DATE_SUB(NOW(), INTERVAL 624 HOUR)),
(18,8,1,'Haze Swivel Desk Chair','haze-swivel-desk-chair','A soft-upholstered swivel chair for home desks that are also in the living room.','Office chairs rarely belong in a living room. This one does: a softly upholstered shell in oat bouclé on a slim four-star oak base that swivels but doesn''t roll.

The seat is tuned for a couple of hours of work at a time, not a full shift.','Bouclé upholstery, foam shell, oak swivel base
80 cm tall · seat height 46 cm
58 × 56 cm footprint',15800,NULL,NULL,NULL,'published',0,'furniture','Haze Swivel Desk Chair | Maato','A soft-upholstered swivel chair for home desks that are also in the living room.',DATE_SUB(NOW(), INTERVAL 432 HOUR),DATE_SUB(NOW(), INTERVAL 432 HOUR)),
(19,9,5,'Walnut C Side Table','walnut-c-side-table','A C-shaped table that slides over the sofa arm, with a shelf for books underneath.','Slide the top over the arm of the sofa or the edge of the bed and the base tucks underneath. There is a shelf for the book you are reading and the two you are pretending to.

Solid walnut, finished with oil, with rounded edges so it doesn''t catch on cushions.','Solid walnut, natural oil
60 cm tall · 45 × 30 cm top
Slides over arms up to 52 cm tall',9600,NULL,NULL,NULL,'published',0,'furniture','Walnut C Side Table | Maato','A C-shaped table that slides over the sofa arm, with a shelf for books underneath.',DATE_SUB(NOW(), INTERVAL 1152 HOUR),DATE_SUB(NOW(), INTERVAL 1152 HOUR)),
(20,9,5,'Floating Oak Shelf','floating-oak-shelf','A solid oak shelf on brass brackets, for books, plants and the good mugs.','One solid board of oak with a softened front edge, carried on two folded brass brackets. It holds 15 kg evenly spread, which is a row of hardbacks or a collection of ceramics.

Fixings for brick and concrete walls are included.','Solid oak, brass-finished steel brackets
20 cm deep · 2.5 cm thick
Holds 15 kg evenly spread
Wall fixings included',3800,NULL,NULL,NULL,'published',0,NULL,'Floating Oak Shelf | Maato','A solid oak shelf on brass brackets, for books, plants and the good mugs.',DATE_SUB(NOW(), INTERVAL 1584 HOUR),DATE_SUB(NOW(), INTERVAL 1584 HOUR)),
(21,8,1,'Dhaka Velvet Sofa','dhaka-velvet-sofa','A three-seater in heavy cotton velvet on tapered oak legs.','The sofa we would buy for ourselves. Deep enough for an afternoon nap, upright enough for guests, and covered in a dense cotton velvet that ages softly rather than wearing through.

It is built to order by Kaath Workshop in Pokhara. Allow three weeks for making and delivery inside the valley.','Kiln-dried hardwood frame, oak legs, cotton velvet
210 cm wide · 92 cm deep · 84 cm tall
Seat height 44 cm
Made to order: about 3 weeks',115000,NULL,NULL,NULL,'published',1,'furniture','Dhaka Velvet Sofa | Maato','A three-seater in heavy cotton velvet on tapered oak legs.',DATE_SUB(NOW(), INTERVAL 912 HOUR),DATE_SUB(NOW(), INTERVAL 912 HOUR)),
(22,4,6,'Stonewashed Linen Duvet Set','stonewashed-linen-duvet-set','A duvet cover and two pillowcases in heavy, stonewashed linen.','Linen is the best fabric for a Kathmandu summer and a Kathmandu winter: cool when it is warm, warm when it is not. Ours is woven in Gorkha and stonewashed so it arrives already soft, with the relaxed creases that linen should have.

The set includes one duvet cover and two Oxford pillowcases, with wooden button closures.','100% linen, 180 gsm, stonewashed
Duvet cover plus 2 pillowcases (50 × 75 cm)
Wooden button closure
Machine wash at 40 °C; line dry',14500,NULL,NULL,NULL,'published',1,'bedding','Stonewashed Linen Duvet Set | Maato','A duvet cover and two pillowcases in heavy, stonewashed linen.',DATE_SUB(NOW(), INTERVAL 2040 HOUR),DATE_SUB(NOW(), INTERVAL 2040 HOUR)),
(23,4,6,'Cotton Percale Sheet Set','cotton-percale-sheet-set','Crisp, cool percale: a fitted sheet, flat sheet and two pillowcases.','If you like a bed that feels like a hotel''s on the first night, percale is the weave. It is matte, crisp and cool, and it gets softer without losing that snap.

Fitted sheets have deep 35 cm pockets that stay on thick mattresses.','100% long-staple cotton, 300 thread count percale
Fitted sheet, flat sheet, 2 pillowcases
35 cm deep pockets
Machine wash warm; tumble dry low',7900,NULL,DATE_SUB(NOW(), INTERVAL 48 HOUR),DATE_ADD(NOW(), INTERVAL 10 DAY),'published',0,'bedding','Cotton Percale Sheet Set | Maato','Crisp, cool percale: a fitted sheet, flat sheet and two pillowcases.',DATE_SUB(NOW(), INTERVAL 1008 HOUR),DATE_SUB(NOW(), INTERVAL 1008 HOUR)),
(24,4,6,'Chunky Hand-knit Throw','chunky-hand-knit-throw','A heavy hand-knit throw in undyed wool, for the end of the bed or the arm of the sofa.','Knitted by hand over about three days, in a thick merino-blend yarn that holds its shape. It is heavy in the way a good blanket should be, and it drapes well over a sofa arm when no one is using it.

The fog colour is undyed; oat is coloured with walnut husk.','80% merino wool, 20% highland wool
130 × 170 cm · about 2.4 kg
Hand-knitted in Gorkha
Dry clean or hand wash cold; dry flat',6800,NULL,NULL,NULL,'published',1,NULL,'Chunky Hand-knit Throw | Maato','A heavy hand-knit throw in undyed wool, for the end of the bed or the arm of the sofa.',DATE_SUB(NOW(), INTERVAL 576 HOUR),DATE_SUB(NOW(), INTERVAL 576 HOUR)),
(25,4,6,'Rumal Linen Cushion Cover','rumal-linen-cushion-cover','A washed-linen cushion cover with a hidden zip. Insert sold separately.','The quickest way to change a room. Washed linen in two colours we keep coming back to: a dusty blush that warms up grey sofas, and a deep teal that sits well against wood.

Hidden zip, overlocked seams, and a slightly oversized cut so cushions look plump rather than stretched.','100% washed linen
Hidden YKK zip
Machine wash at 30 °C
Cover only',1600,NULL,NULL,NULL,'published',0,NULL,'Rumal Linen Cushion Cover | Maato','A washed-linen cushion cover with a hidden zip. Insert sold separately.',DATE_SUB(NOW(), INTERVAL 864 HOUR),DATE_SUB(NOW(), INTERVAL 864 HOUR)),
(26,4,1,'Hollowfibre Pillow','hollowfibre-pillow','A medium-firm pillow that suits back and side sleepers, in a cotton cover.','Not too high, not too flat. The hollowfibre fill springs back after washing, and the cotton cover breathes so it doesn''t trap heat.

It is also a good option for anyone who sneezes around feather pillows.','Cotton cover, hypoallergenic hollowfibre fill
Standard 50 × 75 cm · King 50 × 90 cm
Machine washable at 60 °C',2200,NULL,NULL,NULL,'published',0,'bedding','Hollowfibre Pillow | Maato','A medium-firm pillow that suits back and side sleepers, in a cotton cover.',DATE_SUB(NOW(), INTERVAL 2160 HOUR),DATE_SUB(NOW(), INTERVAL 2160 HOUR)),
(27,4,6,'Kilim Wool Rug','kilim-wool-rug','A flat-woven wool kilim in madder red, indigo and saffron.','Flat-woven on a frame loom, so it is reversible and lies flat without a pad. The pattern comes from a weaving family''s archive of motifs, and the colours are madder, indigo and saffron yellow.

Wool sheds a little for the first few weeks. That is normal and stops on its own.','100% highland wool, natural dyes
Flat weave, reversible
120 × 180, 160 × 230 or 200 × 300 cm
Vacuum without the beater bar; spot clean',18000,NULL,NULL,NULL,'published',0,'rug','Kilim Wool Rug | Maato','A flat-woven wool kilim in madder red, indigo and saffron.',DATE_SUB(NOW(), INTERVAL 672 HOUR),DATE_SUB(NOW(), INTERVAL 672 HOUR)),
(28,5,7,'Cedar & Smoke Candle','cedar-and-smoke-candle','A soy and beeswax candle with notes of Himalayan cedar, smoke and a little cardamom.','The smell of a wood stove in a stone house, with cedar logs stacked by the door. It is poured into a reusable glass in Jumla, with a cotton wick that burns cleanly.

Trim the wick to 5 mm before each burn and let the top melt fully the first time you light it.','Soy and beeswax blend, cotton wick
200 g · about 40 hours, or 400 g · about 75 hours
Reusable glass vessel',2400,NULL,NULL,NULL,'published',1,NULL,'Cedar & Smoke Candle | Maato','A soy and beeswax candle with notes of Himalayan cedar, smoke and a little cardamom.',DATE_SUB(NOW(), INTERVAL 336 HOUR),DATE_SUB(NOW(), INTERVAL 336 HOUR)),
(29,5,7,'Neroli Body Oil','neroli-body-oil','A light, fast-absorbing oil of cold-pressed walnut and neroli for after a hot shower.','Cold-pressed Jumla walnut oil sinks in quickly and doesn''t leave skin greasy. Neroli and a little sweet orange keep the scent bright rather than heavy.

Use a few drops on damp skin straight after a shower.','Walnut, jojoba and sunflower oils, neroli and orange essential oils
100 ml amber glass with dropper
No synthetic fragrance',3100,NULL,NULL,NULL,'published',0,NULL,'Neroli Body Oil | Maato','A light, fast-absorbing oil of cold-pressed walnut and neroli for after a hot shower.',DATE_SUB(NOW(), INTERVAL 528 HOUR),DATE_SUB(NOW(), INTERVAL 528 HOUR)),
(30,5,7,'Lavender Milk Soap, Set of 3','lavender-milk-soap-set-of-3','Cold-process goat milk soap with lavender, wrapped in paper and twine.','Made with fresh goat milk from a farm outside Jumla, cured for six weeks so each bar is hard and long-lasting. The lavender is grown in the same valley.

Gentle enough for faces, sturdy enough for the kitchen sink.','Goat milk, olive and coconut oils, lavender
3 bars, about 100 g each
Palm-oil free',1350,NULL,NULL,NULL,'published',0,NULL,'Lavender Milk Soap, Set of 3 | Maato','Cold-process goat milk soap with lavender, wrapped in paper and twine.',DATE_SUB(NOW(), INTERVAL 240 HOUR),DATE_SUB(NOW(), INTERVAL 240 HOUR)),
(31,5,7,'Amber Kitchen Hand Wash','amber-kitchen-hand-wash','A plant-based hand wash with timur pepper and lemongrass, in a refillable amber bottle.','Timur pepper and lemongrass cut through garlic and onion after cooking. The bottle is amber glass with a steel pump, worth keeping and refilling.

We are pouring the next batch now; it will be back soon.','Plant-based surfactants, timur and lemongrass essential oils
300 ml amber glass with steel pump
Refillable',1600,NULL,NULL,NULL,'published',0,NULL,'Amber Kitchen Hand Wash | Maato','A plant-based hand wash with timur pepper and lemongrass, in a refillable amber bottle.',DATE_SUB(NOW(), INTERVAL 144 HOUR),DATE_SUB(NOW(), INTERVAL 144 HOUR));

INSERT INTO product_images (product_id, path, thumb_path, alt, sort_order) VALUES
(1,'products/chiya-morning-mug-1.jpg','products/chiya-morning-mug-1-thumb.jpg','Chiya Morning Mug',0),
(1,'products/chiya-morning-mug-2.jpg','products/chiya-morning-mug-2-thumb.jpg','Chiya Morning Mug, styled at home',1),
(2,'products/dhunge-speckled-tumblers-set-of-4-1.jpg','products/dhunge-speckled-tumblers-set-of-4-1-thumb.jpg','Dhunge Speckled Tumblers, Set of 4',0),
(2,'products/dhunge-speckled-tumblers-set-of-4-2.jpg','products/dhunge-speckled-tumblers-set-of-4-2-thumb.jpg','Dhunge Speckled Tumblers, Set of 4, styled at home',1),
(3,'products/speckled-bottle-vase-1.jpg','products/speckled-bottle-vase-1-thumb.jpg','Speckled Bottle Vase',0),
(3,'products/speckled-bottle-vase-2.jpg','products/speckled-bottle-vase-2-thumb.jpg','Speckled Bottle Vase, styled at home',1),
(4,'products/ash-glazed-vessel-trio-1.jpg','products/ash-glazed-vessel-trio-1-thumb.jpg','Ash Glazed Vessel Trio',0),
(4,'products/ash-glazed-vessel-trio-2.jpg','products/ash-glazed-vessel-trio-2-thumb.jpg','Ash Glazed Vessel Trio, styled at home',1),
(5,'products/kumal-amphora-vase-1.jpg','products/kumal-amphora-vase-1-thumb.jpg','Kumal Amphora Vase',0),
(5,'products/kumal-amphora-vase-2.jpg','products/kumal-amphora-vase-2-thumb.jpg','Kumal Amphora Vase, styled at home',1),
(6,'products/nilo-stoneware-dinner-plates-1.jpg','products/nilo-stoneware-dinner-plates-1-thumb.jpg','Nilo Stoneware Dinner Plates, Set of 4',0),
(6,'products/nilo-stoneware-dinner-plates-2.jpg','products/nilo-stoneware-dinner-plates-2-thumb.jpg','Nilo Stoneware Dinner Plates, Set of 4, styled at home',1),
(7,'products/twin-bud-vases-with-oak-tray-1.jpg','products/twin-bud-vases-with-oak-tray-1-thumb.jpg','Twin Bud Vases with Oak Tray',0),
(7,'products/twin-bud-vases-with-oak-tray-2.jpg','products/twin-bud-vases-with-oak-tray-2-thumb.jpg','Twin Bud Vases with Oak Tray, styled at home',1),
(8,'products/atelier-enamel-pendant-1.jpg','products/atelier-enamel-pendant-1-thumb.jpg','Atelier Enamel Pendant',0),
(8,'products/atelier-enamel-pendant-2.jpg','products/atelier-enamel-pendant-2-thumb.jpg','Atelier Enamel Pendant, styled at home',1),
(9,'products/studio-task-floor-lamp-1.jpg','products/studio-task-floor-lamp-1-thumb.jpg','Studio Task Floor Lamp',0),
(9,'products/studio-task-floor-lamp-2.jpg','products/studio-task-floor-lamp-2-thumb.jpg','Studio Task Floor Lamp, styled at home',1),
(10,'products/halo-arc-floor-lamp-1.jpg','products/halo-arc-floor-lamp-1-thumb.jpg','Halo Arc Floor Lamp',0),
(10,'products/halo-arc-floor-lamp-2.jpg','products/halo-arc-floor-lamp-2-thumb.jpg','Halo Arc Floor Lamp, styled at home',1),
(11,'products/bet-rattan-pendant-1.jpg','products/bet-rattan-pendant-1-thumb.jpg','Bet Rattan Pendant',0),
(11,'products/bet-rattan-pendant-2.jpg','products/bet-rattan-pendant-2-thumb.jpg','Bet Rattan Pendant, styled at home',1),
(12,'products/brass-bedside-lamp-1.jpg','products/brass-bedside-lamp-1-thumb.jpg','Brass Bedside Lamp',0),
(12,'products/brass-bedside-lamp-2.jpg','products/brass-bedside-lamp-2-thumb.jpg','Brass Bedside Lamp, styled at home',1),
(13,'products/orbit-ring-chandelier-1.jpg','products/orbit-ring-chandelier-1-thumb.jpg','Orbit Ring Chandelier',0),
(13,'products/orbit-ring-chandelier-2.jpg','products/orbit-ring-chandelier-2-thumb.jpg','Orbit Ring Chandelier, styled at home',1),
(14,'products/bhaktapur-oak-bar-stool-1.jpg','products/bhaktapur-oak-bar-stool-1-thumb.jpg','Bhaktapur Oak Bar Stool',0),
(14,'products/bhaktapur-oak-bar-stool-2.jpg','products/bhaktapur-oak-bar-stool-2-thumb.jpg','Bhaktapur Oak Bar Stool, styled at home',1),
(15,'products/shell-side-chair-1.jpg','products/shell-side-chair-1-thumb.jpg','Shell Side Chair',0),
(15,'products/shell-side-chair-2.jpg','products/shell-side-chair-2-thumb.jpg','Shell Side Chair, styled at home',1),
(16,'products/forest-lounge-chair-1.jpg','products/forest-lounge-chair-1-thumb.jpg','Forest Lounge Chair',0),
(16,'products/forest-lounge-chair-2.jpg','products/forest-lounge-chair-2-thumb.jpg','Forest Lounge Chair, styled at home',1),
(17,'products/marigold-reading-armchair-1.jpg','products/marigold-reading-armchair-1-thumb.jpg','Marigold Reading Armchair',0),
(17,'products/marigold-reading-armchair-2.jpg','products/marigold-reading-armchair-2-thumb.jpg','Marigold Reading Armchair, styled at home',1),
(18,'products/haze-swivel-desk-chair-1.jpg','products/haze-swivel-desk-chair-1-thumb.jpg','Haze Swivel Desk Chair',0),
(18,'products/haze-swivel-desk-chair-2.jpg','products/haze-swivel-desk-chair-2-thumb.jpg','Haze Swivel Desk Chair, styled at home',1),
(19,'products/walnut-c-side-table-1.jpg','products/walnut-c-side-table-1-thumb.jpg','Walnut C Side Table',0),
(19,'products/walnut-c-side-table-2.jpg','products/walnut-c-side-table-2-thumb.jpg','Walnut C Side Table, styled at home',1),
(20,'products/floating-oak-shelf-1.jpg','products/floating-oak-shelf-1-thumb.jpg','Floating Oak Shelf',0),
(20,'products/floating-oak-shelf-2.jpg','products/floating-oak-shelf-2-thumb.jpg','Floating Oak Shelf, styled at home',1),
(21,'products/dhaka-velvet-sofa-1.jpg','products/dhaka-velvet-sofa-1-thumb.jpg','Dhaka Velvet Sofa',0),
(21,'products/dhaka-velvet-sofa-2.jpg','products/dhaka-velvet-sofa-2-thumb.jpg','Dhaka Velvet Sofa, styled at home',1),
(22,'products/stonewashed-linen-duvet-set-1.jpg','products/stonewashed-linen-duvet-set-1-thumb.jpg','Stonewashed Linen Duvet Set',0),
(22,'products/stonewashed-linen-duvet-set-2.jpg','products/stonewashed-linen-duvet-set-2-thumb.jpg','Stonewashed Linen Duvet Set, styled at home',1),
(23,'products/cotton-percale-sheet-set-1.jpg','products/cotton-percale-sheet-set-1-thumb.jpg','Cotton Percale Sheet Set',0),
(23,'products/cotton-percale-sheet-set-2.jpg','products/cotton-percale-sheet-set-2-thumb.jpg','Cotton Percale Sheet Set, styled at home',1),
(24,'products/chunky-hand-knit-throw-1.jpg','products/chunky-hand-knit-throw-1-thumb.jpg','Chunky Hand-knit Throw',0),
(24,'products/chunky-hand-knit-throw-2.jpg','products/chunky-hand-knit-throw-2-thumb.jpg','Chunky Hand-knit Throw, styled at home',1),
(25,'products/rumal-linen-cushion-cover-1.jpg','products/rumal-linen-cushion-cover-1-thumb.jpg','Rumal Linen Cushion Cover',0),
(25,'products/rumal-linen-cushion-cover-2.jpg','products/rumal-linen-cushion-cover-2-thumb.jpg','Rumal Linen Cushion Cover, styled at home',1),
(26,'products/hollowfibre-pillow-1.jpg','products/hollowfibre-pillow-1-thumb.jpg','Hollowfibre Pillow',0),
(26,'products/hollowfibre-pillow-2.jpg','products/hollowfibre-pillow-2-thumb.jpg','Hollowfibre Pillow, styled at home',1),
(27,'products/kilim-wool-rug-1.jpg','products/kilim-wool-rug-1-thumb.jpg','Kilim Wool Rug',0),
(27,'products/kilim-wool-rug-2.jpg','products/kilim-wool-rug-2-thumb.jpg','Kilim Wool Rug, styled at home',1),
(28,'products/cedar-and-smoke-candle-1.jpg','products/cedar-and-smoke-candle-1-thumb.jpg','Cedar & Smoke Candle',0),
(28,'products/cedar-and-smoke-candle-2.jpg','products/cedar-and-smoke-candle-2-thumb.jpg','Cedar & Smoke Candle, styled at home',1),
(29,'products/neroli-body-oil-1.jpg','products/neroli-body-oil-1-thumb.jpg','Neroli Body Oil',0),
(29,'products/neroli-body-oil-2.jpg','products/neroli-body-oil-2-thumb.jpg','Neroli Body Oil, styled at home',1),
(30,'products/lavender-milk-soap-set-of-3-1.jpg','products/lavender-milk-soap-set-of-3-1-thumb.jpg','Lavender Milk Soap, Set of 3',0),
(30,'products/lavender-milk-soap-set-of-3-2.jpg','products/lavender-milk-soap-set-of-3-2-thumb.jpg','Lavender Milk Soap, Set of 3, styled at home',1),
(31,'products/amber-kitchen-hand-wash-1.jpg','products/amber-kitchen-hand-wash-1-thumb.jpg','Amber Kitchen Hand Wash',0),
(31,'products/amber-kitchen-hand-wash-2.jpg','products/amber-kitchen-hand-wash-2-thumb.jpg','Amber Kitchen Hand Wash, styled at home',1);

INSERT INTO product_variants (id, product_id, sku, size, color, color_hex, price, sale_price, stock, sort_order) VALUES
(1,1,'MT-CMM-01-CHAL',NULL,'Chalk','#EDE8DF',NULL,NULL,40,0),
(2,1,'MT-CMM-01-CLAY',NULL,'Clay','#B8714D',NULL,NULL,24,1),
(3,1,'MT-CMM-01-ASH',NULL,'Ash','#7D7A74',NULL,NULL,0,2),
(4,2,'MT-DST-02',NULL,NULL,NULL,NULL,NULL,18,0),
(5,3,'MT-SBV-03',NULL,NULL,NULL,NULL,NULL,12,0),
(6,4,'MT-AGV-04',NULL,NULL,NULL,NULL,NULL,7,0),
(7,5,'MT-KAV-05-MEDI','Medium',NULL,NULL,8900,NULL,5,0),
(8,5,'MT-KAV-05-LARG','Large',NULL,NULL,12500,NULL,3,1),
(9,6,'MT-NSD-06-LAKE',NULL,'Lake','#8FA9B8',NULL,NULL,10,0),
(10,6,'MT-NSD-06-SLAT',NULL,'Slate','#4F5A60',NULL,NULL,6,1),
(11,7,'MT-TBV-07',NULL,NULL,NULL,NULL,NULL,10,0),
(12,8,'MT-AEP-08-BONE',NULL,'Bone','#E8E2D6',NULL,NULL,9,0),
(13,8,'MT-AEP-08-GRAP',NULL,'Graphite','#3A3D3F',NULL,NULL,6,1),
(14,9,'MT-STF-09-GRAP',NULL,'Graphite','#3A3D3F',NULL,NULL,4,0),
(15,9,'MT-STF-09-BONE',NULL,'Bone','#E8E2D6',NULL,NULL,2,1),
(16,10,'MT-HAF-10',NULL,NULL,NULL,NULL,NULL,3,0),
(17,11,'MT-BRP-11-SMAL','Small · 40 cm',NULL,NULL,6200,NULL,8,0),
(18,11,'MT-BRP-11-LARG','Large · 55 cm',NULL,NULL,8400,NULL,5,1),
(19,12,'MT-BBL-12',NULL,NULL,NULL,NULL,NULL,11,0),
(20,13,'MT-ORC-13',NULL,NULL,NULL,NULL,NULL,2,0),
(21,14,'MT-BOB-14-COUN','Counter · 65 cm',NULL,NULL,8900,NULL,6,0),
(22,14,'MT-BOB-14-BAR7','Bar · 75 cm',NULL,NULL,9400,NULL,4,1),
(23,15,'MT-SSC-15-CHAL',NULL,'Chalk','#EDE8DF',NULL,NULL,12,0),
(24,15,'MT-SSC-15-BLAC',NULL,'Black','#1E1E1E',NULL,NULL,8,1),
(25,16,'MT-FLC-16-FORE',NULL,'Forest','#2F4A3A',NULL,NULL,2,0),
(26,17,'MT-MRA-17-MARI',NULL,'Marigold','#D9A23B',NULL,NULL,3,0),
(27,18,'MT-HSD-18-OAT',NULL,'Oat','#CFC4B2',NULL,NULL,5,0),
(28,19,'MT-WCS-19',NULL,NULL,NULL,NULL,NULL,7,0),
(29,20,'MT-FOS-20-60CM','60 cm',NULL,NULL,3800,NULL,15,0),
(30,20,'MT-FOS-20-90CM','90 cm',NULL,NULL,4900,NULL,10,1),
(31,20,'MT-FOS-20-120C','120 cm',NULL,NULL,6200,NULL,4,2),
(32,21,'MT-DVS-21-FORE',NULL,'Forest','#2F4A3A',NULL,NULL,2,0),
(33,21,'MT-DVS-21-RUST',NULL,'Rust','#A4471F',NULL,NULL,1,1),
(34,22,'MT-SLD-22-SING-RUST','Single','Rust','#A4471F',14500,NULL,6,0),
(35,22,'MT-SLD-22-SING-OAT','Single','Oat','#D8CCB6',14500,NULL,5,1),
(36,22,'MT-SLD-22-DOUB-RUST','Double','Rust','#A4471F',17500,NULL,4,2),
(37,22,'MT-SLD-22-DOUB-OAT','Double','Oat','#D8CCB6',17500,NULL,7,3),
(38,22,'MT-SLD-22-KING-RUST','King','Rust','#A4471F',19500,NULL,0,4),
(39,22,'MT-SLD-22-KING-OAT','King','Oat','#D8CCB6',19500,NULL,3,5),
(40,23,'MT-CPS-23-SING-WHIT','Single','White','#F7F5F0',7900,6700,9,0),
(41,23,'MT-CPS-23-DOUB-WHIT','Double','White','#F7F5F0',9200,7800,12,1),
(42,23,'MT-CPS-23-KING-WHIT','King','White','#F7F5F0',10500,8900,5,2),
(43,24,'MT-CHK-24-FOG',NULL,'Fog','#9EA3A3',NULL,NULL,9,0),
(44,24,'MT-CHK-24-OAT',NULL,'Oat','#D8CCB6',NULL,NULL,7,1),
(45,25,'MT-RLC-25-4545-BLUS','45 × 45 cm','Blush','#E3B7A6',1600,NULL,14,0),
(46,25,'MT-RLC-25-4545-TEAL','45 × 45 cm','Teal','#2F6F73',1600,NULL,11,1),
(47,25,'MT-RLC-25-5050-BLUS','50 × 50 cm','Blush','#E3B7A6',1850,NULL,8,2),
(48,25,'MT-RLC-25-5050-TEAL','50 × 50 cm','Teal','#2F6F73',1850,NULL,4,3),
(49,26,'MT-HP-26-STAN','Standard',NULL,NULL,2200,NULL,25,0),
(50,26,'MT-HP-26-KING','King',NULL,NULL,2600,NULL,12,1),
(51,27,'MT-KWR-27-1201','120 × 180 cm',NULL,NULL,18000,NULL,4,0),
(52,27,'MT-KWR-27-1602','160 × 230 cm',NULL,NULL,26500,NULL,3,1),
(53,27,'MT-KWR-27-2003','200 × 300 cm',NULL,NULL,38000,NULL,1,2),
(54,28,'MT-CAS-28-200G','200 g',NULL,NULL,2400,NULL,20,0),
(55,28,'MT-CAS-28-400G','400 g',NULL,NULL,3900,NULL,8,1),
(56,29,'MT-NBO-29',NULL,NULL,NULL,NULL,NULL,14,0),
(57,30,'MT-LMS-30',NULL,NULL,NULL,NULL,NULL,30,0),
(58,31,'MT-AKH-31',NULL,NULL,NULL,NULL,NULL,0,0);

INSERT INTO coupons (id, code, description, type, value, min_cart_value, usage_limit, used_count, starts_at, expires_at, is_active) VALUES
(1,'NAMASTE10','10% off your first order over Rs. 3,000','percent',10,3000,500,0,DATE_SUB(NOW(), INTERVAL 720 HOUR),DATE_ADD(NOW(), INTERVAL 90 DAY),1),
(2,'TIHAR1500','Rs. 1,500 off orders over Rs. 15,000','fixed',1500,15000,200,0,DATE_SUB(NOW(), INTERVAL 120 HOUR),DATE_ADD(NOW(), INTERVAL 21 DAY),1),
(3,'MONSOON15','Monsoon sale, ended','percent',15,0,NULL,0,DATE_SUB(NOW(), INTERVAL 2880 HOUR),DATE_SUB(NOW(), INTERVAL 1440 HOUR),1);

INSERT INTO settings (`key`, value) VALUES
('store_name','Maato'),
('store_tagline','Homeware made slowly, across Nepal.'),
('logo_path',''),
('currency_code','NPR'),
('currency_symbol','Rs.'),
('tax_rate','13'),
('tax_label','VAT'),
('shipping_fee_valley','150'),
('shipping_fee_outside','350'),
('free_shipping_threshold','10000'),
('valley_districts','Kathmandu,Lalitpur,Bhaktapur'),
('pickup_enabled','1'),
('pickup_address','Maato Studio, Jhamsikhel Road, Lalitpur 44600 (Sun–Fri, 10:00–18:00)'),
('support_email','hello@maato.test'),
('support_phone','+977 1-5520417'),
('low_stock_threshold','5');

INSERT INTO orders (id, order_number, user_id, status, payment_method, subtotal, discount_total, shipping_total, tax_rate, tax_total, grand_total, currency, coupon_id, coupon_code, customer_name, customer_email, customer_phone, ship_recipient, ship_phone, ship_line1, ship_line2, ship_city, ship_district, ship_province, ship_postal_code, note, cancelled_at, delivered_at, created_at, updated_at) VALUES
(1,'MT010247',3,'delivered','cash_on_delivery',4500,0,150,13,585,5235,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 2014 HOUR),DATE_SUB(NOW(), INTERVAL 2086 HOUR),DATE_SUB(NOW(), INTERVAL 2086 HOUR)),
(2,'MT010254',4,'delivered','cash_on_delivery',47900,4790,0,13,5604.3,48714.3,'NPR',1,'NAMASTE10','Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1996 HOUR),DATE_SUB(NOW(), INTERVAL 2068 HOUR),DATE_SUB(NOW(), INTERVAL 2068 HOUR)),
(3,'MT010261',3,'delivered','cash_on_delivery',14500,1450,0,13,1696.5,14746.5,'NPR',1,'NAMASTE10','Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1940 HOUR),DATE_SUB(NOW(), INTERVAL 2012 HOUR),DATE_SUB(NOW(), INTERVAL 2012 HOUR)),
(4,'MT010268',3,'delivered','cash_on_delivery',13900,0,0,13,1807,15707,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1925 HOUR),DATE_SUB(NOW(), INTERVAL 1997 HOUR),DATE_SUB(NOW(), INTERVAL 1997 HOUR)),
(5,'MT010275',4,'cancelled','cash_on_delivery',5400,0,350,13,702,6452,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,DATE_SUB(NOW(), INTERVAL 1807 HOUR),NULL,DATE_SUB(NOW(), INTERVAL 1810 HOUR),DATE_SUB(NOW(), INTERVAL 1810 HOUR)),
(6,'MT010282',3,'delivered','cash_on_delivery',7200,720,150,13,842.4,7472.4,'NPR',1,'NAMASTE10','Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1708 HOUR),DATE_SUB(NOW(), INTERVAL 1780 HOUR),DATE_SUB(NOW(), INTERVAL 1780 HOUR)),
(7,'MT010289',3,'delivered','pay_on_pickup',27500,0,0,13,3575,31075,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1647 HOUR),DATE_SUB(NOW(), INTERVAL 1719 HOUR),DATE_SUB(NOW(), INTERVAL 1719 HOUR)),
(8,'MT010296',4,'delivered','cash_on_delivery',15900,0,0,13,2067,17967,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1641 HOUR),DATE_SUB(NOW(), INTERVAL 1713 HOUR),DATE_SUB(NOW(), INTERVAL 1713 HOUR)),
(9,'MT010303',3,'delivered','cash_on_delivery',46050,0,0,13,5986.5,52036.5,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1572 HOUR),DATE_SUB(NOW(), INTERVAL 1644 HOUR),DATE_SUB(NOW(), INTERVAL 1644 HOUR)),
(10,'MT010310',3,'delivered','cash_on_delivery',39000,0,0,13,5070,44070,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1560 HOUR),DATE_SUB(NOW(), INTERVAL 1632 HOUR),DATE_SUB(NOW(), INTERVAL 1632 HOUR)),
(11,'MT010317',4,'delivered','cash_on_delivery',39300,3930,0,13,4598.1,39968.1,'NPR',1,'NAMASTE10','Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1541 HOUR),DATE_SUB(NOW(), INTERVAL 1613 HOUR),DATE_SUB(NOW(), INTERVAL 1613 HOUR)),
(12,'MT010324',3,'delivered','cash_on_delivery',25300,2530,0,13,2960.1,25730.1,'NPR',1,'NAMASTE10','Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1496 HOUR),DATE_SUB(NOW(), INTERVAL 1568 HOUR),DATE_SUB(NOW(), INTERVAL 1568 HOUR)),
(13,'MT010331',3,'delivered','cash_on_delivery',35400,0,0,13,4602,40002,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1484 HOUR),DATE_SUB(NOW(), INTERVAL 1556 HOUR),DATE_SUB(NOW(), INTERVAL 1556 HOUR)),
(14,'MT010338',4,'delivered','cash_on_delivery',10400,0,0,13,1352,11752,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1481 HOUR),DATE_SUB(NOW(), INTERVAL 1553 HOUR),DATE_SUB(NOW(), INTERVAL 1553 HOUR)),
(15,'MT010345',3,'cancelled','cash_on_delivery',11800,0,0,13,1534,13334,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,DATE_SUB(NOW(), INTERVAL 1312 HOUR),NULL,DATE_SUB(NOW(), INTERVAL 1315 HOUR),DATE_SUB(NOW(), INTERVAL 1315 HOUR)),
(16,'MT010352',3,'delivered','cash_on_delivery',7450,0,150,13,968.5,8568.5,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1228 HOUR),DATE_SUB(NOW(), INTERVAL 1300 HOUR),DATE_SUB(NOW(), INTERVAL 1300 HOUR)),
(17,'MT010359',4,'delivered','cash_on_delivery',140800,0,0,13,18304,159104,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1222 HOUR),DATE_SUB(NOW(), INTERVAL 1294 HOUR),DATE_SUB(NOW(), INTERVAL 1294 HOUR)),
(18,'MT010366',3,'delivered','cash_on_delivery',34100,0,0,13,4433,38533,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1188 HOUR),DATE_SUB(NOW(), INTERVAL 1260 HOUR),DATE_SUB(NOW(), INTERVAL 1260 HOUR)),
(19,'MT010373',3,'delivered','pay_on_pickup',53100,0,0,13,6903,60003,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1155 HOUR),DATE_SUB(NOW(), INTERVAL 1227 HOUR),DATE_SUB(NOW(), INTERVAL 1227 HOUR)),
(20,'MT010380',4,'cancelled','cash_on_delivery',21300,0,0,13,2769,24069,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,DATE_SUB(NOW(), INTERVAL 1216 HOUR),NULL,DATE_SUB(NOW(), INTERVAL 1219 HOUR),DATE_SUB(NOW(), INTERVAL 1219 HOUR)),
(21,'MT010387',3,'delivered','cash_on_delivery',25800,2580,0,13,3018.6,26238.6,'NPR',1,'NAMASTE10','Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1127 HOUR),DATE_SUB(NOW(), INTERVAL 1199 HOUR),DATE_SUB(NOW(), INTERVAL 1199 HOUR)),
(22,'MT010394',3,'delivered','pay_on_pickup',13700,1370,0,13,1602.9,13932.9,'NPR',1,'NAMASTE10','Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1070 HOUR),DATE_SUB(NOW(), INTERVAL 1142 HOUR),DATE_SUB(NOW(), INTERVAL 1142 HOUR)),
(23,'MT010401',4,'delivered','cash_on_delivery',34100,0,0,13,4433,38533,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 1030 HOUR),DATE_SUB(NOW(), INTERVAL 1102 HOUR),DATE_SUB(NOW(), INTERVAL 1102 HOUR)),
(24,'MT010408',3,'delivered','cash_on_delivery',36400,0,0,13,4732,41132,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 960 HOUR),DATE_SUB(NOW(), INTERVAL 1032 HOUR),DATE_SUB(NOW(), INTERVAL 1032 HOUR)),
(25,'MT010415',3,'delivered','cash_on_delivery',8900,0,150,13,1157,10207,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 900 HOUR),DATE_SUB(NOW(), INTERVAL 972 HOUR),DATE_SUB(NOW(), INTERVAL 972 HOUR)),
(26,'MT010422',4,'delivered','cash_on_delivery',1850,0,350,13,240.5,2440.5,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 720 HOUR),DATE_SUB(NOW(), INTERVAL 792 HOUR),DATE_SUB(NOW(), INTERVAL 792 HOUR)),
(27,'MT010429',3,'delivered','cash_on_delivery',23700,2370,0,13,2772.9,24102.9,'NPR',1,'NAMASTE10','Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 615 HOUR),DATE_SUB(NOW(), INTERVAL 687 HOUR),DATE_SUB(NOW(), INTERVAL 687 HOUR)),
(28,'MT010436',3,'cancelled','cash_on_delivery',17450,0,0,13,2268.5,19718.5,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,DATE_SUB(NOW(), INTERVAL 641 HOUR),NULL,DATE_SUB(NOW(), INTERVAL 644 HOUR),DATE_SUB(NOW(), INTERVAL 644 HOUR)),
(29,'MT010443',4,'delivered','cash_on_delivery',22900,0,0,13,2977,25877,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 475 HOUR),DATE_SUB(NOW(), INTERVAL 547 HOUR),DATE_SUB(NOW(), INTERVAL 547 HOUR)),
(30,'MT010450',3,'delivered','cash_on_delivery',15800,0,0,13,2054,17854,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 339 HOUR),DATE_SUB(NOW(), INTERVAL 411 HOUR),DATE_SUB(NOW(), INTERVAL 411 HOUR)),
(31,'MT010457',3,'delivered','cash_on_delivery',15850,0,0,13,2060.5,17910.5,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 311 HOUR),DATE_SUB(NOW(), INTERVAL 383 HOUR),DATE_SUB(NOW(), INTERVAL 383 HOUR)),
(32,'MT010464',4,'delivered','cash_on_delivery',19500,0,0,13,2535,22035,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 299 HOUR),DATE_SUB(NOW(), INTERVAL 371 HOUR),DATE_SUB(NOW(), INTERVAL 371 HOUR)),
(33,'MT010471',3,'delivered','cash_on_delivery',28400,0,0,13,3692,32092,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 294 HOUR),DATE_SUB(NOW(), INTERVAL 366 HOUR),DATE_SUB(NOW(), INTERVAL 366 HOUR)),
(34,'MT010478',3,'delivered','cash_on_delivery',4050,0,150,13,526.5,4726.5,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 287 HOUR),DATE_SUB(NOW(), INTERVAL 359 HOUR),DATE_SUB(NOW(), INTERVAL 359 HOUR)),
(35,'MT010485',4,'delivered','cash_on_delivery',5400,0,350,13,702,6452,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 272 HOUR),DATE_SUB(NOW(), INTERVAL 344 HOUR),DATE_SUB(NOW(), INTERVAL 344 HOUR)),
(36,'MT010492',3,'delivered','cash_on_delivery',40200,0,0,13,5226,45426,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 210 HOUR),DATE_SUB(NOW(), INTERVAL 282 HOUR),DATE_SUB(NOW(), INTERVAL 282 HOUR)),
(37,'MT010499',3,'cancelled','cash_on_delivery',25200,0,0,13,3276,28476,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,DATE_SUB(NOW(), INTERVAL 260 HOUR),NULL,DATE_SUB(NOW(), INTERVAL 263 HOUR),DATE_SUB(NOW(), INTERVAL 263 HOUR)),
(38,'MT010506',4,'delivered','cash_on_delivery',1850,0,350,13,240.5,2440.5,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 145 HOUR),DATE_SUB(NOW(), INTERVAL 217 HOUR),DATE_SUB(NOW(), INTERVAL 217 HOUR)),
(39,'MT010513',3,'delivered','cash_on_delivery',13800,0,0,13,1794,15594,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 124 HOUR),DATE_SUB(NOW(), INTERVAL 196 HOUR),DATE_SUB(NOW(), INTERVAL 196 HOUR)),
(40,'MT010520',3,'delivered','cash_on_delivery',36800,0,0,13,4784,41584,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 86 HOUR),DATE_SUB(NOW(), INTERVAL 158 HOUR),DATE_SUB(NOW(), INTERVAL 158 HOUR)),
(41,'MT010527',4,'delivered','cash_on_delivery',3700,0,350,13,481,4531,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,DATE_SUB(NOW(), INTERVAL 83 HOUR),DATE_SUB(NOW(), INTERVAL 155 HOUR),DATE_SUB(NOW(), INTERVAL 155 HOUR)),
(42,'MT010534',5,'confirmed','cash_on_delivery',6800,0,150,13,884,7834,'NPR',NULL,NULL,'Sneha Rai','sneha@example.com','9860456789','Sneha Rai','9860456789','Suryabinayak 3, Sirutar',NULL,'Bhaktapur','Bhaktapur','Bagmati','44800',NULL,NULL,NULL,DATE_SUB(NOW(), INTERVAL 59 HOUR),DATE_SUB(NOW(), INTERVAL 59 HOUR)),
(43,'MT010541',3,'packed','cash_on_delivery',17800,0,0,13,2314,20114,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,NULL,DATE_SUB(NOW(), INTERVAL 50 HOUR),DATE_SUB(NOW(), INTERVAL 50 HOUR)),
(44,'MT010548',4,'confirmed','cash_on_delivery',26150,0,0,13,3399.5,29549.5,'NPR',NULL,NULL,'Bikash Thapa','bikash@example.com','9851098765','Bikash Thapa','9851098765','Lakeside Road, Ward 6','Opposite Moondance','Pokhara','Kaski','Gandaki','33700',NULL,NULL,NULL,DATE_SUB(NOW(), INTERVAL 20 HOUR),DATE_SUB(NOW(), INTERVAL 20 HOUR)),
(45,'MT010555',5,'pending','pay_on_pickup',9600,0,0,13,1248,10848,'NPR',NULL,NULL,'Sneha Rai','sneha@example.com','9860456789','Sneha Rai','9860456789','Suryabinayak 3, Sirutar',NULL,'Bhaktapur','Bhaktapur','Bagmati','44800',NULL,NULL,NULL,DATE_SUB(NOW(), INTERVAL 5 HOUR),DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(46,'MT010562',3,'pending','cash_on_delivery',16100,0,0,13,2093,18193,'NPR',NULL,NULL,'Aarati Gurung','aarati@example.com','9841122334','Aarati Gurung','9841122334','House 14, Bakhundole Marg',NULL,'Lalitpur','Lalitpur','Bagmati','44700',NULL,NULL,NULL,DATE_SUB(NOW(), INTERVAL 2 HOUR),DATE_SUB(NOW(), INTERVAL 2 HOUR));

INSERT INTO order_items (order_id, product_id, variant_id, product_name, variant_label, sku, image_path, unit_price, quantity, line_total) VALUES
(1,1,1,'Chiya Morning Mug','Chalk','MT-CMM-01-CHAL','products/chiya-morning-mug-1-thumb.jpg',1450,2,2900),
(1,25,46,'Rumal Linen Cushion Cover','45 × 45 cm / Teal','MT-RLC-25-4545-TEAL','products/rumal-linen-cushion-cover-1-thumb.jpg',1600,1,1600),
(2,16,25,'Forest Lounge Chair','Forest','MT-FLC-16-FORE','products/forest-lounge-chair-1-thumb.jpg',38500,1,38500),
(2,14,22,'Bhaktapur Oak Bar Stool','Bar · 75 cm','MT-BOB-14-BAR7','products/bhaktapur-oak-bar-stool-1-thumb.jpg',9400,1,9400),
(3,22,34,'Stonewashed Linen Duvet Set','Single / Rust','MT-SLD-22-SING-RUST','products/stonewashed-linen-duvet-set-1-thumb.jpg',14500,1,14500),
(4,28,54,'Cedar & Smoke Candle','200 g','MT-CAS-28-200G','products/cedar-and-smoke-candle-1-thumb.jpg',2400,3,7200),
(4,23,40,'Cotton Percale Sheet Set','Single / White','MT-CPS-23-SING-WHIT','products/cotton-percale-sheet-set-1-thumb.jpg',6700,1,6700),
(5,4,6,'Ash Glazed Vessel Trio',NULL,'MT-AGV-04','products/ash-glazed-vessel-trio-1-thumb.jpg',5400,1,5400),
(6,15,23,'Shell Side Chair','Chalk','MT-SSC-15-CHAL','products/shell-side-chair-1-thumb.jpg',7200,1,7200),
(7,17,26,'Marigold Reading Armchair','Marigold','MT-MRA-17-MARI','products/marigold-reading-armchair-1-thumb.jpg',27500,1,27500),
(8,10,16,'Halo Arc Floor Lamp',NULL,'MT-HAF-10','products/halo-arc-floor-lamp-1-thumb.jpg',15900,1,15900),
(9,1,2,'Chiya Morning Mug','Clay','MT-CMM-01-CLAY','products/chiya-morning-mug-1-thumb.jpg',1450,3,4350),
(9,25,47,'Rumal Linen Cushion Cover','50 × 50 cm / Blush','MT-RLC-25-5050-BLUS','products/rumal-linen-cushion-cover-1-thumb.jpg',1850,2,3700),
(9,27,53,'Kilim Wool Rug','200 × 300 cm','MT-KWR-27-2003','products/kilim-wool-rug-1-thumb.jpg',38000,1,38000),
(10,22,35,'Stonewashed Linen Duvet Set','Single / Oat','MT-SLD-22-SING-OAT','products/stonewashed-linen-duvet-set-1-thumb.jpg',14500,1,14500),
(10,13,20,'Orbit Ring Chandelier',NULL,'MT-ORC-13','products/orbit-ring-chandelier-1-thumb.jpg',24500,1,24500),
(11,27,51,'Kilim Wool Rug','120 × 180 cm','MT-KWR-27-1201','products/kilim-wool-rug-1-thumb.jpg',18000,1,18000),
(11,10,16,'Halo Arc Floor Lamp',NULL,'MT-HAF-10','products/halo-arc-floor-lamp-1-thumb.jpg',15900,1,15900),
(11,4,6,'Ash Glazed Vessel Trio',NULL,'MT-AGV-04','products/ash-glazed-vessel-trio-1-thumb.jpg',5400,1,5400),
(12,8,12,'Atelier Enamel Pendant','Bone','MT-AEP-08-BONE','products/atelier-enamel-pendant-1-thumb.jpg',7800,1,7800),
(12,22,37,'Stonewashed Linen Duvet Set','Double / Oat','MT-SLD-22-DOUB-OAT','products/stonewashed-linen-duvet-set-1-thumb.jpg',17500,1,17500),
(13,27,52,'Kilim Wool Rug','160 × 230 cm','MT-KWR-27-1602','products/kilim-wool-rug-1-thumb.jpg',26500,1,26500),
(13,5,7,'Kumal Amphora Vase','Medium','MT-KAV-05-MEDI','products/kumal-amphora-vase-1-thumb.jpg',8900,1,8900),
(14,31,58,'Amber Kitchen Hand Wash',NULL,'MT-AKH-31','products/amber-kitchen-hand-wash-1-thumb.jpg',1600,3,4800),
(14,6,9,'Nilo Stoneware Dinner Plates, Set of 4','Lake','MT-NSD-06-LAKE','products/nilo-stoneware-dinner-plates-1-thumb.jpg',5600,1,5600),
(15,28,54,'Cedar & Smoke Candle','200 g','MT-CAS-28-200G','products/cedar-and-smoke-candle-1-thumb.jpg',2400,1,2400),
(15,14,22,'Bhaktapur Oak Bar Stool','Bar · 75 cm','MT-BOB-14-BAR7','products/bhaktapur-oak-bar-stool-1-thumb.jpg',9400,1,9400),
(16,6,10,'Nilo Stoneware Dinner Plates, Set of 4','Slate','MT-NSD-06-SLAT','products/nilo-stoneware-dinner-plates-1-thumb.jpg',5600,1,5600),
(16,25,48,'Rumal Linen Cushion Cover','50 × 50 cm / Teal','MT-RLC-25-5050-TEAL','products/rumal-linen-cushion-cover-1-thumb.jpg',1850,1,1850),
(17,27,51,'Kilim Wool Rug','120 × 180 cm','MT-KWR-27-1201','products/kilim-wool-rug-1-thumb.jpg',18000,1,18000),
(17,23,41,'Cotton Percale Sheet Set','Double / White','MT-CPS-23-DOUB-WHIT','products/cotton-percale-sheet-set-1-thumb.jpg',7800,1,7800),
(17,21,33,'Dhaka Velvet Sofa','Rust','MT-DVS-21-RUST','products/dhaka-velvet-sofa-1-thumb.jpg',115000,1,115000),
(18,1,3,'Chiya Morning Mug','Ash','MT-CMM-01-ASH','products/chiya-morning-mug-1-thumb.jpg',1450,2,2900),
(18,13,20,'Orbit Ring Chandelier',NULL,'MT-ORC-13','products/orbit-ring-chandelier-1-thumb.jpg',24500,1,24500),
(18,23,40,'Cotton Percale Sheet Set','Single / White','MT-CPS-23-SING-WHIT','products/cotton-percale-sheet-set-1-thumb.jpg',6700,1,6700),
(19,27,53,'Kilim Wool Rug','200 × 300 cm','MT-KWR-27-2003','products/kilim-wool-rug-1-thumb.jpg',38000,1,38000),
(19,14,21,'Bhaktapur Oak Bar Stool','Counter · 65 cm','MT-BOB-14-COUN','products/bhaktapur-oak-bar-stool-1-thumb.jpg',8900,1,8900),
(19,20,31,'Floating Oak Shelf','120 cm','MT-FOS-20-120C','products/floating-oak-shelf-1-thumb.jpg',6200,1,6200),
(20,10,16,'Halo Arc Floor Lamp',NULL,'MT-HAF-10','products/halo-arc-floor-lamp-1-thumb.jpg',15900,1,15900),
(20,4,6,'Ash Glazed Vessel Trio',NULL,'MT-AGV-04','products/ash-glazed-vessel-trio-1-thumb.jpg',5400,1,5400),
(21,27,51,'Kilim Wool Rug','120 × 180 cm','MT-KWR-27-1201','products/kilim-wool-rug-1-thumb.jpg',18000,1,18000),
(21,8,12,'Atelier Enamel Pendant','Bone','MT-AEP-08-BONE','products/atelier-enamel-pendant-1-thumb.jpg',7800,1,7800),
(22,25,46,'Rumal Linen Cushion Cover','45 × 45 cm / Teal','MT-RLC-25-4545-TEAL','products/rumal-linen-cushion-cover-1-thumb.jpg',1600,3,4800),
(22,5,7,'Kumal Amphora Vase','Medium','MT-KAV-05-MEDI','products/kumal-amphora-vase-1-thumb.jpg',8900,1,8900),
(23,17,26,'Marigold Reading Armchair','Marigold','MT-MRA-17-MARI','products/marigold-reading-armchair-1-thumb.jpg',27500,1,27500),
(23,26,49,'Hollowfibre Pillow','Standard','MT-HP-26-STAN','products/hollowfibre-pillow-1-thumb.jpg',2200,3,6600),
(24,23,42,'Cotton Percale Sheet Set','King / White','MT-CPS-23-KING-WHIT','products/cotton-percale-sheet-set-1-thumb.jpg',8900,1,8900),
(24,28,55,'Cedar & Smoke Candle','400 g','MT-CAS-28-400G','products/cedar-and-smoke-candle-1-thumb.jpg',3900,3,11700),
(24,18,27,'Haze Swivel Desk Chair','Oat','MT-HSD-18-OAT','products/haze-swivel-desk-chair-1-thumb.jpg',15800,1,15800),
(25,23,42,'Cotton Percale Sheet Set','King / White','MT-CPS-23-KING-WHIT','products/cotton-percale-sheet-set-1-thumb.jpg',8900,1,8900),
(26,25,48,'Rumal Linen Cushion Cover','50 × 50 cm / Teal','MT-RLC-25-5050-TEAL','products/rumal-linen-cushion-cover-1-thumb.jpg',1850,1,1850),
(27,22,37,'Stonewashed Linen Duvet Set','Double / Oat','MT-SLD-22-DOUB-OAT','products/stonewashed-linen-duvet-set-1-thumb.jpg',17500,1,17500),
(27,11,17,'Bet Rattan Pendant','Small · 40 cm','MT-BRP-11-SMAL','products/bet-rattan-pendant-1-thumb.jpg',6200,1,6200),
(28,22,34,'Stonewashed Linen Duvet Set','Single / Rust','MT-SLD-22-SING-RUST','products/stonewashed-linen-duvet-set-1-thumb.jpg',14500,1,14500),
(28,30,57,'Lavender Milk Soap, Set of 3',NULL,'MT-LMS-30','products/lavender-milk-soap-set-of-3-1-thumb.jpg',1350,1,1350),
(28,25,45,'Rumal Linen Cushion Cover','45 × 45 cm / Blush','MT-RLC-25-4545-BLUS','products/rumal-linen-cushion-cover-1-thumb.jpg',1600,1,1600),
(29,4,6,'Ash Glazed Vessel Trio',NULL,'MT-AGV-04','products/ash-glazed-vessel-trio-1-thumb.jpg',5400,1,5400),
(29,22,37,'Stonewashed Linen Duvet Set','Double / Oat','MT-SLD-22-DOUB-OAT','products/stonewashed-linen-duvet-set-1-thumb.jpg',17500,1,17500),
(30,18,27,'Haze Swivel Desk Chair','Oat','MT-HSD-18-OAT','products/haze-swivel-desk-chair-1-thumb.jpg',15800,1,15800),
(31,25,47,'Rumal Linen Cushion Cover','50 × 50 cm / Blush','MT-RLC-25-5050-BLUS','products/rumal-linen-cushion-cover-1-thumb.jpg',1850,1,1850),
(31,15,24,'Shell Side Chair','Black','MT-SSC-15-BLAC','products/shell-side-chair-1-thumb.jpg',7200,1,7200),
(31,24,44,'Chunky Hand-knit Throw','Oat','MT-CHK-24-OAT','products/chunky-hand-knit-throw-1-thumb.jpg',6800,1,6800),
(32,22,38,'Stonewashed Linen Duvet Set','King / Rust','MT-SLD-22-KING-RUST','products/stonewashed-linen-duvet-set-1-thumb.jpg',19500,1,19500),
(33,23,41,'Cotton Percale Sheet Set','Double / White','MT-CPS-23-DOUB-WHIT','products/cotton-percale-sheet-set-1-thumb.jpg',7800,1,7800),
(33,25,45,'Rumal Linen Cushion Cover','45 × 45 cm / Blush','MT-RLC-25-4545-BLUS','products/rumal-linen-cushion-cover-1-thumb.jpg',1600,3,4800),
(33,18,27,'Haze Swivel Desk Chair','Oat','MT-HSD-18-OAT','products/haze-swivel-desk-chair-1-thumb.jpg',15800,1,15800),
(34,30,57,'Lavender Milk Soap, Set of 3',NULL,'MT-LMS-30','products/lavender-milk-soap-set-of-3-1-thumb.jpg',1350,3,4050),
(35,12,19,'Brass Bedside Lamp',NULL,'MT-BBL-12','products/brass-bedside-lamp-1-thumb.jpg',5400,1,5400),
(36,22,37,'Stonewashed Linen Duvet Set','Double / Oat','MT-SLD-22-DOUB-OAT','products/stonewashed-linen-duvet-set-1-thumb.jpg',17500,1,17500),
(36,26,50,'Hollowfibre Pillow','King','MT-HP-26-KING','products/hollowfibre-pillow-1-thumb.jpg',2600,2,5200),
(36,22,36,'Stonewashed Linen Duvet Set','Double / Rust','MT-SLD-22-DOUB-RUST','products/stonewashed-linen-duvet-set-1-thumb.jpg',17500,1,17500),
(37,2,4,'Dhunge Speckled Tumblers, Set of 4',NULL,'MT-DST-02','products/dhunge-speckled-tumblers-set-of-4-1-thumb.jpg',2800,3,8400),
(37,19,28,'Walnut C Side Table',NULL,'MT-WCS-19','products/walnut-c-side-table-1-thumb.jpg',9600,1,9600),
(37,15,23,'Shell Side Chair','Chalk','MT-SSC-15-CHAL','products/shell-side-chair-1-thumb.jpg',7200,1,7200),
(38,25,47,'Rumal Linen Cushion Cover','50 × 50 cm / Blush','MT-RLC-25-5050-BLUS','products/rumal-linen-cushion-cover-1-thumb.jpg',1850,1,1850),
(39,20,29,'Floating Oak Shelf','60 cm','MT-FOS-20-60CM','products/floating-oak-shelf-1-thumb.jpg',3800,2,7600),
(39,29,56,'Neroli Body Oil',NULL,'MT-NBO-29','products/neroli-body-oil-1-thumb.jpg',3100,2,6200),
(40,17,26,'Marigold Reading Armchair','Marigold','MT-MRA-17-MARI','products/marigold-reading-armchair-1-thumb.jpg',27500,1,27500),
(40,29,56,'Neroli Body Oil',NULL,'MT-NBO-29','products/neroli-body-oil-1-thumb.jpg',3100,3,9300),
(41,25,48,'Rumal Linen Cushion Cover','50 × 50 cm / Teal','MT-RLC-25-5050-TEAL','products/rumal-linen-cushion-cover-1-thumb.jpg',1850,2,3700),
(42,24,43,'Chunky Hand-knit Throw','Fog','MT-CHK-24-FOG','products/chunky-hand-knit-throw-1-thumb.jpg',6800,1,6800),
(43,20,31,'Floating Oak Shelf','120 cm','MT-FOS-20-120C','products/floating-oak-shelf-1-thumb.jpg',6200,1,6200),
(43,11,18,'Bet Rattan Pendant','Large · 55 cm','MT-BRP-11-LARG','products/bet-rattan-pendant-1-thumb.jpg',8400,1,8400),
(43,25,45,'Rumal Linen Cushion Cover','45 × 45 cm / Blush','MT-RLC-25-4545-BLUS','products/rumal-linen-cushion-cover-1-thumb.jpg',1600,2,3200),
(44,1,3,'Chiya Morning Mug','Ash','MT-CMM-01-ASH','products/chiya-morning-mug-1-thumb.jpg',1450,3,4350),
(44,29,56,'Neroli Body Oil',NULL,'MT-NBO-29','products/neroli-body-oil-1-thumb.jpg',3100,3,9300),
(44,9,14,'Studio Task Floor Lamp','Graphite','MT-STF-09-GRAP','products/studio-task-floor-lamp-1-thumb.jpg',12500,1,12500),
(45,19,28,'Walnut C Side Table',NULL,'MT-WCS-19','products/walnut-c-side-table-1-thumb.jpg',9600,1,9600),
(46,15,23,'Shell Side Chair','Chalk','MT-SSC-15-CHAL','products/shell-side-chair-1-thumb.jpg',7200,1,7200),
(46,23,42,'Cotton Percale Sheet Set','King / White','MT-CPS-23-KING-WHIT','products/cotton-percale-sheet-set-1-thumb.jpg',8900,1,8900);

INSERT INTO order_status_history (order_id, status, note, changed_by, created_at) VALUES
(1,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 2086 HOUR)),
(1,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 2066 HOUR)),
(1,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 2046 HOUR)),
(1,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 2026 HOUR)),
(1,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 2006 HOUR)),
(2,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 2068 HOUR)),
(2,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 2048 HOUR)),
(2,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 2028 HOUR)),
(2,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 2008 HOUR)),
(2,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1988 HOUR)),
(3,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 2012 HOUR)),
(3,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1992 HOUR)),
(3,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1972 HOUR)),
(3,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1952 HOUR)),
(3,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1932 HOUR)),
(4,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1997 HOUR)),
(4,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1977 HOUR)),
(4,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1957 HOUR)),
(4,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1937 HOUR)),
(4,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1917 HOUR)),
(5,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1810 HOUR)),
(5,'cancelled','Cancelled at the customer''s request.',NULL,DATE_SUB(NOW(), INTERVAL 1790 HOUR)),
(6,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1780 HOUR)),
(6,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1760 HOUR)),
(6,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1740 HOUR)),
(6,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1720 HOUR)),
(6,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1700 HOUR)),
(7,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1719 HOUR)),
(7,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1699 HOUR)),
(7,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1679 HOUR)),
(7,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1659 HOUR)),
(7,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1639 HOUR)),
(8,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1713 HOUR)),
(8,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1693 HOUR)),
(8,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1673 HOUR)),
(8,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1653 HOUR)),
(8,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1633 HOUR)),
(9,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1644 HOUR)),
(9,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1624 HOUR)),
(9,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1604 HOUR)),
(9,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1584 HOUR)),
(9,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1564 HOUR)),
(10,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1632 HOUR)),
(10,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1612 HOUR)),
(10,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1592 HOUR)),
(10,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1572 HOUR)),
(10,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1552 HOUR)),
(11,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1613 HOUR)),
(11,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1593 HOUR)),
(11,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1573 HOUR)),
(11,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1553 HOUR)),
(11,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1533 HOUR)),
(12,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1568 HOUR)),
(12,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1548 HOUR)),
(12,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1528 HOUR)),
(12,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1508 HOUR)),
(12,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1488 HOUR)),
(13,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1556 HOUR)),
(13,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1536 HOUR)),
(13,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1516 HOUR)),
(13,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1496 HOUR)),
(13,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1476 HOUR)),
(14,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1553 HOUR)),
(14,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1533 HOUR)),
(14,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1513 HOUR)),
(14,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1493 HOUR)),
(14,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1473 HOUR)),
(15,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1315 HOUR)),
(15,'cancelled','Cancelled at the customer''s request.',NULL,DATE_SUB(NOW(), INTERVAL 1295 HOUR)),
(16,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1300 HOUR)),
(16,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1280 HOUR)),
(16,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1260 HOUR)),
(16,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1240 HOUR)),
(16,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1220 HOUR)),
(17,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1294 HOUR)),
(17,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1274 HOUR)),
(17,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1254 HOUR)),
(17,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1234 HOUR)),
(17,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1214 HOUR)),
(18,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1260 HOUR)),
(18,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1240 HOUR)),
(18,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1220 HOUR)),
(18,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1200 HOUR)),
(18,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1180 HOUR)),
(19,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1227 HOUR)),
(19,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1207 HOUR)),
(19,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1187 HOUR)),
(19,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1167 HOUR)),
(19,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1147 HOUR)),
(20,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1219 HOUR)),
(20,'cancelled','Cancelled at the customer''s request.',NULL,DATE_SUB(NOW(), INTERVAL 1199 HOUR)),
(21,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1199 HOUR)),
(21,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1179 HOUR)),
(21,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1159 HOUR)),
(21,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1139 HOUR)),
(21,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1119 HOUR)),
(22,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1142 HOUR)),
(22,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1122 HOUR)),
(22,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1102 HOUR)),
(22,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1082 HOUR)),
(22,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1062 HOUR)),
(23,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1102 HOUR)),
(23,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1082 HOUR)),
(23,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 1062 HOUR)),
(23,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 1042 HOUR)),
(23,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 1022 HOUR)),
(24,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 1032 HOUR)),
(24,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 1012 HOUR)),
(24,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 992 HOUR)),
(24,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 972 HOUR)),
(24,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 952 HOUR)),
(25,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 972 HOUR)),
(25,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 952 HOUR)),
(25,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 932 HOUR)),
(25,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 912 HOUR)),
(25,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 892 HOUR)),
(26,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 792 HOUR)),
(26,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 772 HOUR)),
(26,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 752 HOUR)),
(26,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 732 HOUR)),
(26,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 712 HOUR)),
(27,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 687 HOUR)),
(27,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 667 HOUR)),
(27,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 647 HOUR)),
(27,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 627 HOUR)),
(27,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 607 HOUR)),
(28,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 644 HOUR)),
(28,'cancelled','Cancelled at the customer''s request.',NULL,DATE_SUB(NOW(), INTERVAL 624 HOUR)),
(29,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 547 HOUR)),
(29,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 527 HOUR)),
(29,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 507 HOUR)),
(29,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 487 HOUR)),
(29,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 467 HOUR)),
(30,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 411 HOUR)),
(30,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 391 HOUR)),
(30,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 371 HOUR)),
(30,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 351 HOUR)),
(30,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 331 HOUR)),
(31,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 383 HOUR)),
(31,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 363 HOUR)),
(31,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 343 HOUR)),
(31,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 323 HOUR)),
(31,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 303 HOUR)),
(32,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 371 HOUR)),
(32,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 351 HOUR)),
(32,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 331 HOUR)),
(32,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 311 HOUR)),
(32,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 291 HOUR)),
(33,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 366 HOUR)),
(33,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 346 HOUR)),
(33,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 326 HOUR)),
(33,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 306 HOUR)),
(33,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 286 HOUR)),
(34,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 359 HOUR)),
(34,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 339 HOUR)),
(34,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 319 HOUR)),
(34,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 299 HOUR)),
(34,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 279 HOUR)),
(35,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 344 HOUR)),
(35,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 324 HOUR)),
(35,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 304 HOUR)),
(35,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 284 HOUR)),
(35,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 264 HOUR)),
(36,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 282 HOUR)),
(36,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 262 HOUR)),
(36,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 242 HOUR)),
(36,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 222 HOUR)),
(36,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 202 HOUR)),
(37,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 263 HOUR)),
(37,'cancelled','Cancelled at the customer''s request.',NULL,DATE_SUB(NOW(), INTERVAL 243 HOUR)),
(38,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 217 HOUR)),
(38,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 197 HOUR)),
(38,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 177 HOUR)),
(38,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 157 HOUR)),
(38,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 137 HOUR)),
(39,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 196 HOUR)),
(39,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 176 HOUR)),
(39,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 156 HOUR)),
(39,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 136 HOUR)),
(39,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 116 HOUR)),
(40,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 158 HOUR)),
(40,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 138 HOUR)),
(40,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 118 HOUR)),
(40,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 98 HOUR)),
(40,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 78 HOUR)),
(41,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 155 HOUR)),
(41,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 135 HOUR)),
(41,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 115 HOUR)),
(41,'shipped','Handed to our delivery partner.',2,DATE_SUB(NOW(), INTERVAL 95 HOUR)),
(41,'delivered','Delivered and paid in cash.',1,DATE_SUB(NOW(), INTERVAL 75 HOUR)),
(42,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 59 HOUR)),
(42,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 39 HOUR)),
(43,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 50 HOUR)),
(43,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 30 HOUR)),
(43,'packed','Packed and labelled at the studio.',1,DATE_SUB(NOW(), INTERVAL 10 HOUR)),
(44,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 20 HOUR)),
(44,'confirmed','Confirmed by phone with the customer.',2,DATE_SUB(NOW(), INTERVAL 0 HOUR)),
(45,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(46,'pending','Order placed.',NULL,DATE_SUB(NOW(), INTERVAL 2 HOUR));

UPDATE coupons SET used_count = (SELECT COUNT(*) FROM orders WHERE orders.coupon_id = coupons.id AND orders.status <> 'cancelled');

INSERT INTO reviews (product_id, user_id, rating, title, body, status, created_at) VALUES
(1,3,5,'Better than the photos','The glaze has so much more depth in person. It feels heavy in the best way and has become the first thing I reach for in the morning.','approved',DATE_SUB(NOW(), INTERVAL 1164 HOUR)),
(25,3,5,'Exactly what I hoped','Arrived well packed in Pokhara, two days after ordering. Solid, beautifully finished and the colour is spot on.','approved',DATE_SUB(NOW(), INTERVAL 270 HOUR)),
(22,3,4,'Lovely, slightly smaller than expected','Really well made. Measure your space first, it is a touch more compact than I imagined, but I would buy it again.','approved',DATE_SUB(NOW(), INTERVAL 186 HOUR)),
(28,3,5,'Gift that got noticed','Bought this for my mother and now my aunt wants one. The finish is lovely and it clearly was made by hand.','approved',DATE_SUB(NOW(), INTERVAL 936 HOUR)),
(23,3,4,'Quiet and well made','Nothing flashy, just a well-made thing that looks right in the room. Delivery person called ahead, which I appreciated.','approved',DATE_SUB(NOW(), INTERVAL 270 HOUR)),
(15,3,5,'Softer after every wash','Three washes in and it keeps getting better. Worth the price.','approved',DATE_SUB(NOW(), INTERVAL 287 HOUR)),
(17,3,5,'Better than the photos','The glaze has so much more depth in person. It feels heavy in the best way and has become the first thing I reach for in the morning.','approved',DATE_SUB(NOW(), INTERVAL 62 HOUR)),
(27,3,5,'Exactly what I hoped','Arrived well packed in Pokhara, two days after ordering. Solid, beautifully finished and the colour is spot on.','approved',DATE_SUB(NOW(), INTERVAL 1103 HOUR)),
(13,3,4,'Lovely, slightly smaller than expected','Really well made. Measure your space first, it is a touch more compact than I imagined, but I would buy it again.','approved',DATE_SUB(NOW(), INTERVAL 1164 HOUR)),
(8,3,5,'Gift that got noticed','Bought this for my mother and now my aunt wants one. The finish is lovely and it clearly was made by hand.','approved',DATE_SUB(NOW(), INTERVAL 1103 HOUR)),
(5,3,4,'Quiet and well made','Nothing flashy, just a well-made thing that looks right in the room. Delivery person called ahead, which I appreciated.','approved',DATE_SUB(NOW(), INTERVAL 1046 HOUR)),
(6,3,5,'Softer after every wash','Three washes in and it keeps getting better. Worth the price.','approved',DATE_SUB(NOW(), INTERVAL 1204 HOUR)),
(14,3,5,'Better than the photos','The glaze has so much more depth in person. It feels heavy in the best way and has become the first thing I reach for in the morning.','pending',DATE_SUB(NOW(), INTERVAL 1131 HOUR)),
(20,3,5,'Exactly what I hoped','Arrived well packed in Pokhara, two days after ordering. Solid, beautifully finished and the colour is spot on.','pending',DATE_SUB(NOW(), INTERVAL 100 HOUR));

INSERT INTO wishlists (user_id, product_id) VALUES (3,16),(3,21),(3,27),(4,8),(4,10),(5,24);

INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, meta, ip_address, created_at) VALUES
(1,'settings.updated','settings',NULL,'Updated store settings',NULL,'127.0.0.1',DATE_SUB(NOW(), INTERVAL 1680 HOUR)),
(1,'coupon.created','coupon',2,'Created coupon TIHAR1500',NULL,'127.0.0.1',DATE_SUB(NOW(), INTERVAL 120 HOUR)),
(2,'product.updated','product',10,'Updated product Halo Arc Floor Lamp','{"fields":["sale_price"]}','127.0.0.1',DATE_SUB(NOW(), INTERVAL 72 HOUR)),
(2,'product.created','product',7,'Created product Twin Bud Vases with Oak Tray',NULL,'127.0.0.1',DATE_SUB(NOW(), INTERVAL 48 HOUR));

SET FOREIGN_KEY_CHECKS = 1;
