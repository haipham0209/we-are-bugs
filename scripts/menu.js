//nav===========================================================================

document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const overlay = document.querySelector('.overlay');
    const menuIcon = document.querySelector('.menu-icon');

    // Mở/đóng menu khi nhấp vào nút
    menuToggle.addEventListener('click', function () {
        navMenu.classList.toggle('open');
        menuIcon.classList.toggle('open'); // Thay đổi biểu tượng
    });

    // Đóng menu khi nhấp vào overlay
    overlay.addEventListener('click', function () {
        navMenu.classList.remove('open');
        menuIcon.classList.remove('open'); // Đặt lại biểu tượng về ban đầu
    });
});



//showmore btn=======================================================================
// Lấy tất cả các nút "Show More"
const showMoreBtns = document.querySelectorAll('.show-more-btn');


showMoreBtns.forEach(button => {
    button.addEventListener('click', () => {
        // Lấy nhóm sản phẩm mà nút này thuộc về
        const group = button.getAttribute('data-group');
        const productShowcase = document.querySelector(`#${group} .product-showcase`);

        
        productShowcase.classList.toggle('open');

        
        if (productShowcase.classList.contains('open')) {
            button.textContent = 'Show Less';
        } else {
            button.textContent = 'Show More';
        }
    });
});

//filter button===========================================================================
    
    // const filterButtons = document.querySelectorAll('.filter-button');

    // filterButtons.forEach(button => {
    //     button.addEventListener('click', () => {
    //         const targetId = button.getAttribute('data-target');
    //         const targetSection = document.querySelector(targetId);

    //         if (targetSection) {
    //             // Kéo đến vị trí của phần tử mục tiêu
    //             targetSection.scrollIntoView({
    //                 behavior: 'smooth', // hiệu ứng cuộn mượt
    //                 block: 'start' // cuộn đến đầu phần tử
    //             });
    //         }
    //     });
    // });

    //filter button===========================================================================

    // document.addEventListener('DOMContentLoaded', function () {
    //     // filter button===========================================================================
    
    //     const filterButtons = document.querySelectorAll('.filter-button');
    
    //     filterButtons.forEach(button => {
    //         button.addEventListener('click', () => {
    //             const targetId = button.getAttribute('data-target');
    //             const targetSection = document.querySelector(targetId);
    
    //             if (targetSection) {
    //                 // Kéo đến vị trí của phần tử mục tiêu
    //                 const headerOffset = document.querySelector('.header') ? document.querySelector('.header').offsetHeight : 0; // Thay '.header' bằng selector của header của bạn
    //                 const elementPosition = targetSection.getBoundingClientRect().top;
    //                 const offsetPosition = elementPosition + window.pageYOffset - headerOffset; // Thêm bù
    
    //                 window.scrollTo({
    //                     top: offsetPosition,
    //                     behavior: 'smooth' // hiệu ứng cuộn mượt
    //                 });
    //             } else {
    //                 console.error(`No target section found for ID: ${targetId}`);
    //             }
    //         });
    //     });
    // });
    

//////////////////////////////////////////////////////////show more button/////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
    const groups = document.querySelectorAll(".group");

    groups.forEach((group) => {
        const productShowcase = group.querySelector(".product-showcase");
        const productContents = group.querySelectorAll(".product-content");
        const showMoreButton = group.querySelector(".show-more-btn");

        // Ẩn nút "Show More" nếu số sản phẩm <= 3
        if (productContents.length <= 3) {
            showMoreButton.style.display = "none";
        } else {
            // Chỉ hiển thị 3 sản phẩm ban đầu
            productContents.forEach((product, index) => {
                if (index >= 3) product.style.display = "none";
            });

            let isExpanded = false; // Trạng thái mở rộng

            showMoreButton.addEventListener("click", () => {
                if (isExpanded) {
                    // Thu gọn: Loại bỏ class 'open'
                    productShowcase.classList.remove("open");
                    setTimeout(() => {
                        // Ẩn các sản phẩm sau 3 sản phẩm sau khi hiệu ứng chạy xong
                        productContents.forEach((product, index) => {
                            if (index >= 3) product.style.display = "none";
                        });
                    }, 600); // Đợi 0.6s cho hiệu ứng chạy xong
                    showMoreButton.textContent = "Show More";
                } else {
                    // Mở rộng: Hiển thị tất cả sản phẩm và thêm class 'open'
                    productContents.forEach((product) => (product.style.display = "block"));
                    productShowcase.classList.add("open");
                    showMoreButton.textContent = "Show Less";
                }
                isExpanded = !isExpanded;
            });
        }
    });
});




