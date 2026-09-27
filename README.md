# Shopio 🛒

## منصة تجارة إلكترونية مبنية باستخدام Docker و REST API

## مقدمة عن المشروع

Shopio هو نظام متجر إلكتروني حديث تم تطويره بالاعتماد على معمارية فصل الواجهة الأمامية عن الخلفية (Frontend / Backend Separation).

بدأ المشروع كنظام PHP تقليدي يعتمد على صفحات Server-Side Rendering، ثم تمت إعادة هيكلته وتحويله إلى نظام حديث يعتمد على:

- REST API
- واجهة Frontend مستقلة
- Docker Containers
- قاعدة بيانات MariaDB
- مصادقة JWT
- تخزين دائم للبيانات والصور


---

# فكرة النظام

النظام يتكون من ثلاثة أجزاء رئيسية:

1. الواجهة الأمامية (Frontend)

وهي الجزء الذي يتعامل معه المستخدم، وتم تطويره باستخدام:

- HTML
- CSS
- JavaScript


وظيفتها:

- عرض المنتجات
- تسجيل الدخول
- إنشاء حساب
- إدارة السلة
- لوحة تحكم المدير


---

2. الخادم الخلفي (Backend)

تم تطويره باستخدام:

- PHP 8.2
- REST API
- PHP-FPM


وظيفته:

- معالجة الطلبات
- التحقق من المستخدمين
- إدارة المنتجات
- إدارة السلة
- التحكم بالصلاحيات


---

3. قاعدة البيانات (Database)

تم استخدام:

MariaDB 10.4


وتستخدم لحفظ:

- بيانات المستخدمين
- المنتجات
- السلة
- بيانات النظام


---

# معمارية المشروع

تم تصميم النظام بالشكل التالي:

المستخدم
    |
    |
  Nginx
    |
    |
REST API Backend
    |
    |
MariaDB Database



حيث:

- Nginx هو نقطة الدخول الوحيدة للنظام.
- Backend غير مكشوف مباشرة للمستخدم.
- Database معزولة داخل شبكة Docker.


---

# هيكل المشروع

Shopio

│
├── api
│ ├── Controllers
│ ├── Helpers
│ ├── Middleware
│ └── Database Connection
│
├── client
│ ├── صفحات HTML
│ ├── ملفات JavaScript
│ ├── ملفات CSS
│ └── صور المنتجات
│
├── database
│ └── init.sql
│
├── docker
│ ├── إعدادات Nginx
│ └── إعدادات PHP
│
├── legacy
│ └── النسخة القديمة من المشروع
│
├── Dockerfile
│
├── docker-compose.yml
│
└── README.md



---

# مميزات النظام


## للمستخدم

- إنشاء حساب جديد
- تسجيل الدخول
- مشاهدة المنتجات
- إضافة المنتجات إلى السلة
- حذف المنتجات من السلة
- إدارة الجلسة باستخدام JWT


---

## للمدير (Admin)

- تسجيل الدخول كمدير
- إضافة المنتجات
- تعديل المنتجات
- حذف المنتجات
- رفع صور المنتجات
- إدارة المستخدمين


---

# نظام المصادقة والحماية

تم استخدام JWT Authentication.

عند تسجيل الدخول:

1. يتم إرسال البريد وكلمة المرور.
2. يتم التحقق من البيانات.
3. يتم إنشاء Token خاص بالمستخدم.
4. يتم استخدام Token للوصول إلى العمليات المحمية.


مثال:
Authorization: Bearer TOKEN



---

# Docker Architecture


يحتوي المشروع على أربع حاويات:


## 1- Backend Container

المسؤول عن:

- تشغيل API
- تنفيذ منطق النظام
- الاتصال بقاعدة البيانات


يعمل باستخدام المستخدم:
www-data


وليس Root لزيادة الأمان.


---

## 2- Nginx Container

المسؤول عن:

- عرض ملفات الواجهة.
- استقبال طلبات المستخدم.
- تحويل طلبات API إلى Backend.


المنفذ المفتوح:
80


---

## 3- Database Container

MariaDB:
3306

لكن المنفذ غير مكشوف خارج Docker.

الاتصال يتم فقط داخل شبكة Docker.


---

## 4- Image Initialization Container

وظيفته:

- تجهيز صلاحيات مجلد الصور.
- ضمان قدرة Backend على رفع الصور.


---

# التخزين الدائم (Persistent Storage)


تم استخدام Docker Volumes:


قاعدة البيانات:
project_db_data

صور المنتجات:

project_img_data



وهذا يعني أن البيانات لا تضيع عند:

- إعادة تشغيل الحاويات.
- حذف الحاويات.
- إعادة بناء المشروع.


---

# الاختبارات التي تم تنفيذها


## اختبار تسجيل الدخول

تم اختبار:

✔ بيانات صحيحة  
✔ بيانات خاطئة  
✔ إنشاء JWT Token  


---

## اختبار المنتجات

تم اختبار:

✔ جلب المنتجات  
✔ إضافة منتج  
✔ تعديل منتج  
✔ حذف منتج  
✔ رفع الصور


---

## اختبار السلة

تم اختبار:

✔ إضافة منتج للسلة  
✔ عرض السلة  
✔ حذف منتج من السلة


---

## اختبار الأعطال

تم اختبار:

- توقف Backend Container
- توقف Database Container
- إعادة تشغيل النظام بالكامل


والنتيجة:

النظام يستعيد العمل بدون فقد البيانات.


---

# أمن النظام


تم تطبيق:


✔ عزل الشبكة بين الحاويات

✔ عدم كشف قاعدة البيانات للخارج

✔ عدم تشغيل Backend بصلاحية Root

✔ إخفاء ملف .env

✔ منع ظهور أخطاء السيرفر للمستخدم

✔ حماية المسارات الخاصة بالمدير


---

# تشغيل المشروع


بعد تثبيت Docker:


تشغيل النظام:
docker compose up -d



عرض حالة الحاويات:
docker compose ps



إيقاف النظام:
docker compose down



---

# الوصول للنظام


الواجهة الرئيسية:
http://localhost



صفحة تسجيل الدخول:


http://localhost/login.html



صفحة التسجيل:


http://localhost/register.html



لوحة المدير:


http://localhost/admin.html



---

# النسخة القديمة Legacy


تم الاحتفاظ بالنسخة القديمة داخل:


legacy/



السبب:

- الحفاظ على تاريخ المشروع.
- الرجوع إليها عند الحاجة.
- فصلها عن المعمارية الحديثة.


---

# Docker Registry

The Shopio backend image is published on Docker Hub.

## Image

`nizarabdo/ecommerce-backend`

## Available Tags

- `v1.0.0` — Versioned release
- `latest` — Latest stable release

## Pull from Docker Hub

Pull the versioned release:

`docker pull nizarabdo/ecommerce-backend:v1.0.0`

Or pull the latest stable release:

`docker pull nizarabdo/ecommerce-backend:latest`

## Run the Complete Application

The backend image is used by `docker-compose.yml`. To start the complete Shopio stack:

`docker compose up -d`

Verify the running services:

`docker compose ps`

The application is available through the Nginx reverse proxy at:

`http://localhost`

The backend and database are not published directly to the host.

## Registry Verification

The `v1.0.0` image was successfully removed locally, pulled again from Docker Hub, and used to start the complete application.

Database data remained persistent through the named volume after the containers were recreated.

# الخلاصة


Shopio يمثل انتقالاً من تطبيق PHP تقليدي إلى نظام حديث يعتمد على:

- REST API
- Docker
- Nginx
- MariaDB
- JWT Authentication
- Frontend مستقل


مما يجعل النظام أكثر:

- أماناً
- سهولة في التطوير
- قابلية للتوسع
- جاهزية للنشر على الخوادم
