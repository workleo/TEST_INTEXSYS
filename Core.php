<?php

/**
 * Front controller
 *
 * @author Unknown Author <unknown@authors.com>
 */
class Core
{
    /**
     * Pdo
     *
     * @var PDO
     */
    private $database;

    /**
     * Core constructor
     */
    public function __construct()
    {
        try {
        $this->database = new PDO('sqlite:/tmp/foo.db');        
        $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Handle
     *
     * @return void
     */
    public function handle()
    {
        $url = $_SERVER['REQUEST_URI'];

        $product = $this->getProductByUrl($url);

        if ($product) {

            $response['type'] = 'product';
            $response['title'] = $product['name'];
            $response['product'] = $product;

            require_once(__DIR__ . '/templates/product_page.phtml');
        } else {
            $response['type'] = 'index';
            $response['title'] = 'Products';
            $response['products'] = $this->getProducts();
            require_once(__DIR__ . '/templates/index.phtml');
        }
    }

    /**
     * Get product list page
     *
     * @return array
     */
    public function getProducts()
    {
        $statement = $this->database->prepare('SELECT * FROM products');
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Get product by url
     *
     * @param string $url
     *
     * @return array
     */
    public function getProductByUrl($url)
    {
        $statement = $this->database->prepare('SELECT * FROM products WHERE url = :url');
        $statement->bindValue(':url', $url);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Reset
     *
     * @return void
     */
    public function resetDatabase()
    {
        $products = [
            [
                1,
                '/bushnell-speedster-iii-radar-gun-w-rf-technology-101921-w-optional-speed-screen-bushn.html',
                'opplanet-bushnell-radar-gun-speedster-3.jpg',
                'Bushnell Speedster III Radar Gun 101921 w/ Free S&H',
                'The Bushnell Speedster III Radar Gun is the perfect tool for tracking speed. Developed with years of research and experience at Bushnell, and available exclusively through OpticsPlanet, the Speedster 3 gives athletes, coaches, trainers, hobbyists and more a cost-effective tool for monitoring performance.',
                43.99
            ],
            [
                2,
                '/g-seven-br2-ballistic-rangefinder.html',
                'opplanet-g-seven-br2-ballistic-rangefinder-e1011-main.jpg',
                'G Seven BR2 Ballistic Rangefinder',
                'How do you improve the best piece of gear for long range shooting? First, tighten up the beam divergence and tune up the software to get better target discrimination and detection at extreme ranges. Then redesign the programming menu to simplify in-the-field updates to any of the five customizable ballistic profiles; and finally, add MRAD units to existing MOA and BDC outputs to create a complete ballistic solver for drop and wind.',
                410.10
            ],
            [
                3,
                '/u-s-optics-b-25-5-25x52-digital-ffp-34mm-tube-riflescope.html',
                'opplanet-u-s-optics-b-25-5-25x52mm-riflescope-horus-h37-reticle-100-click-elevation-knob-with-1-10-mil-adjustment-b-25-h37-main.jpg',
                'Digital FFP 34mm Tube Riflescope',
                'The U.S. Optics B-25 5-25x52 Digital FFP 34mm Tube Riflescope was designed to function as the ideal scope for any person requiring an exceptional improvement for their firearm. These Rifle Scopes from the optical specialists at U.S. Optics are formulated using the durable and long lasting components which you have come to count on from this impressive organization.',
                89.04
            ],
            [
                4,
                '/armasight-opmod-predator-336-2-8x25-weapon-sight-flir-tau-2-core-tan-tat173wn2op.html',
                'opplanet-armasight-opmod-predator-336-2-8x25-wpn-sgt-flir-tau-2-cr-tan-tat173wn2op0022-main-1.jpg',
                'Armasight OPMOD Predator',
                'The Armasight OPMOD Predator 336 2-8x25 Thermal Imaging Scope is a super compact, light-weight gun-monted thermal imager. The product experts of OPMOD and Armasight have teamed up to bring you this unique, exclusive tan finish and a strategically designed scope.',
                34.70
            ]
        ];


        $this->database->exec('DROP TABLE IF EXISTS products');
        $this->database->exec('CREATE TABLE products (id integer PRIMARY KEY, url varchar(255), image varchar(255), name varchar(255), description text, price DOUBLE)');

        $this->database->exec('DROP TABLE IF EXISTS review');
        $this->database->exec('CREATE TABLE review (  id INTEGER  PRIMARY KEY AUTOINCREMENT,productId REFERENCES products (id),name VARCHAR (255),email VARCHAR (255), content   TEXT)');


        foreach ($products as $product) {
            $this->database->exec(
                sprintf(
                    'INSERT INTO products (id, url, image, name, description, price) VALUES ("%s")',
                    implode('", "', $product)
                )
            );
        }
    }  
}
