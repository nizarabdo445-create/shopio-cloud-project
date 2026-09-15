<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>متجر إلكتروني مظلم</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background-color: #1e293b;
            padding: 30px;
            position: fixed;
            height: 100%;
            border-left: 1px solid #334155; /* Changed for RTL */
            box-shadow: -4px 0 15px rgba(0,0,0,0.2); /* Changed for RTL */
            right: 0; /* Changed for RTL */
        }

        .sidebar nav a {
            color: #94a3b8;
            text-decoration: none;
            display: block;
            padding: 14px 20px;
            margin: 12px 0;
            border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar nav a:hover {
            background: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%);
            color: white;
            transform: translateX(-8px); /* Changed for RTL */
        }

        .content {
            margin-right: 280px; /* Changed for RTL */
            padding: 40px;
            width: calc(100% - 280px);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            padding: 20px;
        }

        .product-card {
            background: #1e293b;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #334155;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.3);
            border-color: #818cf8;
        }

        .product-image {
            width: 100%;
            height: 240px;
            object-fit: contain;
            border-radius: 12px;
            margin-bottom: 20px;
            background: #0f172a;
            padding: 15px;
        }

        .product-name {
            margin: 15px 0;
            font-size: 1.2rem;
            font-weight: 600;
            color: #e2e8f0;
        }

        .product-price {
            color: #10b981;
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: 0.5px;
        }

        .add-to-cart {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
            transition: all 0.3s;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .add-to-cart:hover {
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
            transform: scale(1.02);
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: #1e293b;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }

        .cart-table th, .cart-table td {
            padding: 18px;
            text-align: right; /* Changed for RTL */
            border-bottom: 1px solid #334155;
        }

        .cart-table th {
            background: #4f46e5;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cart-table tr:hover {
            background-color: #2d3748;
        }

        .remove-item {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .remove-item:hover {
            background: #dc2626;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        h2 {
            color: #e2e8f0;
            margin-bottom: 40px;
            font-size: 2rem;
            position: relative;
            padding-bottom: 10px;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0; /* Changed for RTL */
            width: 60px;
            height: 3px;
            background: #818cf8;
            border-radius: 2px;
        }

        #cartCount {
            color: #818cf8;
            font-weight: 700;
            font-size: 1.3rem;
            margin-right: 8px; /* Changed for RTL */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .product-card {
            animation: fadeIn 0.6s ease-out;
        }

        .notification {
            background: #10b981;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
            padding: 16px 32px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }
        
        .carousel-container {
            position: relative;
            max-width: 1200px;
            margin: 30px auto;
            overflow: hidden;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
            gap: 25px;
            padding: 20px 0;
        }

        .product-card {
            min-width: 280px;
            flex-shrink: 0;
            background: #2d2d2d;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #3d3d3d;
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(59, 130, 246, 0.7);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .carousel-btn:hover {
            background: #3b82f6;
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-prev {
            right: -60px;
        }

        .carousel-next {
            left: -60px;
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2 style="color: #818cf8; margin-bottom: 40px;">TechLaptops</h2>
            <nav>
                <a href="#products">المنتجات</a>
                <a href="#cart">سلة المشتريات</a>
            </nav>
            <div style="margin-top: 40px; padding: 20px; background: #1e293b; border-radius: 12px;">
                <span>العناصر في السلة: </span>
                <span id="cartCount">0</span>
            </div>
        </aside>

        <main class="content" id="products">
            <h2>المنتجات المتاحة</h2>
            <div class="carousel-container">
                <div class="carousel-track">
                    <?php
                    include('config.php');
$result= mysqli_query($con, "SELECT * FROM products" );
while($row = mysqli_fetch_array($result)){
                    $imagePath = '/' . $row['prod_img']; // Fix image path to root
                    echo"
                    <div class='product-card'>
                        <img src='$imagePath' class='product-image' alt=' $row[prod_name]'>
                        <h3 class='product-name'> $row[prod_name]</h3>
                        <p class='product-price'>$row[prod_price] ريال</p>
                        <button onclick='addToCart($row[id])' class='add-to-cart'>إضافة إلى السلة</button>
                    </div>
                    ";
                }
                    
                    
                    ?>
                    
                    
                </div>
                
                <button class="carousel-btn carousel-prev">‹</button>
                <button class="carousel-btn carousel-next">›</button>
            </div>
        </main>

        <main class="content" id="cart" style="display: none;">
            <h2>سلة المشتريات</h2>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>السعر</th>
                        <th>الكمية</th>
                        <th>الإجمالي</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody id="cartItems"></tbody>
            </table>
        </main>
    </div>

    <script>
        let cartItems = [];

        function addToCart(productId) {
            // Function logic would go here
            showNotification(`تم الإضافة للسلة`);
        }

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.style.position = 'fixed';
            notification.style.bottom = '40px';
            notification.style.right = '40px';
            notification.innerHTML = `
                <span style="font-size: 1.2em;">✓</span>
                ${message}
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }

        document.querySelectorAll('.sidebar nav a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                document.querySelectorAll('.content').forEach(content => {
                    content.style.display = 'none';
                });
                document.querySelector(link.getAttribute('href')).style.display = 'block';
            });
        });
        
        // دوار المنتجات
        const track = document.querySelector('.carousel-track');
        const cards = document.querySelectorAll('.product-card');
        const cardWidth = cards[0].offsetWidth + 25; // عرض البطاقة + الجاب
        let currentPosition = 0;

        document.querySelector('.carousel-next').addEventListener('click', () => {
            if (currentPosition > -(cardWidth * (cards.length - 1))) {
                currentPosition -= cardWidth;
                track.style.transform = `translateX(${currentPosition}px)`;
            }
        });

        document.querySelector('.carousel-prev').addEventListener('click', () => {
            if (currentPosition < 0) {
                currentPosition += cardWidth;
                track.style.transform = `translateX(${currentPosition}px)`;
            }
        });
    </script>
</body>
</html>
