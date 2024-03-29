<style>
    .label-container {
        position: fixed;
        bottom: 48px;
        right: 105px;
        display: table;
        visibility: hidden;
    }

    .label-text {
        color: #FFF;
        background: rgba(51, 51, 51, 0.5);
        display: table-cell;
        vertical-align: middle;
        padding: 10px;
        border-radius: 3px;
    }

    .label-arrow {
        display: table-cell;
        vertical-align: middle;
        color: #333;
        opacity: 0.5;
    }

    .float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #F33;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        box-shadow: 2px 2px 3px #999;
        z-index: 1000;
        animation: bot-to-top 2s ease-out;
    }

    ul {
        position: fixed;
        right: 40px;
        padding-bottom: 20px;
        bottom: 80px;
        z-index: 100;
    }

    ul li {
        list-style: none;
        margin-bottom: 10px;
    }

    ul li a {
        background-color: #F33;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        box-shadow: 2px 2px 3px #999;
        width: 60px;
        height: 60px;
        display: block;
    }

    ul:hover {
        visibility: visible !important;
        opacity: 1 !important;
    }


    .my-float {
        font-size: 24px;
        margin-top: 18px;
    }

    a#menu-start + ul {
        visibility: hidden;
    }

    a#menu-start:hover + ul {
        visibility: visible;
        animation: scale-in 0.5s;
    }

    a#menu-start i {
        animation: rotate-in 0.5s;
    }

    a#menu-start:hover > i {
        animation: rotate-out 0.5s;
    }

    @keyframes bot-to-top {
        0% {
            bottom: -40px
        }
        50% {
            bottom: 40px
        }
    }

    @keyframes scale-in {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes rotate-in {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes rotate-out {
        from {
            transform: rotate(360deg);
        }
        to {
            transform: rotate(0deg);
        }
    }
</style>

<script>
    $(document).ready(function () {
        $("#menu-close").click(function () {
            window.close();
        });
    });
</script>

<a href="#" class="float" id="menu-start">
    <i class="fa fa-user my-float"></i>
</a>
<ul>
    <li><a href="#" id="menu-close">
            <i class="fa fa-times my-float"></i>
        </a></li>
    <!--li><a href="#" id="menu-2">
        </a></li>
    <li><a href="#" id="menu-3">
            <i class="fa fa-phone my-float"></i-->
        </a></li>
</ul>