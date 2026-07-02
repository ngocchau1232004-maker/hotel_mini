    
    
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded",function(){
            const btn=document.getElementById("toggleSidebar");
            const sidebar=document.querySelector(".sidebar");
            const content=document.querySelector(".content");
            if(btn){
                btn.addEventListener("click",function(){
                    sidebar.classList.toggle("collapsed");
                    content.classList.toggle("expanded");
                });
            }
        });
    </script>

</body>
</html>