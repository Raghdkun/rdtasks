<div id="infyLoader" class="infy-loader">
    <div class="modern-loader">
        <div class="logo-spinner">
            <img src="{{ asset('assets/img/globallogo.png') }}" alt="Loading..." class="spinning-logo">
        </div>
        <div class="loading-text">
            <span class="loading-dots">Loading</span>
        </div>
    </div>
</div>

<style>
.infy-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.modern-loader {
    text-align: center;
    animation: fadeInUp 0.6s ease-out;
}

.logo-spinner {
    position: relative;
    margin-bottom: 20px;
}

.spinning-logo {
    width: 60px;
    height: 60px;
    object-fit: contain;
    animation: modernSpin 2s linear infinite;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
}

.loading-text {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 16px;
    font-weight: 500;
    color: #6c757d;
    margin-top: 15px;
}

.loading-dots::after {
    content: '';
    animation: dots 1.5s steps(4, end) infinite;
}

@keyframes modernSpin {
    0% {
        transform: rotate(0deg) scale(1);
    }
    50% {
        transform: rotate(180deg) scale(1.1);
    }
    100% {
        transform: rotate(360deg) scale(1);
    }
}

@keyframes dots {
    0%, 20% {
        content: '';
    }
    40% {
        content: '.';
    }
    60% {
        content: '..';
    }
    80%, 100% {
        content: '...';
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .infy-loader {
    }
    
    .loading-text {
        color: #adb5bd;
    }
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .spinning-logo {
        width: 50px;
        height: 50px;
    }
    
    .loading-text {
        font-size: 14px;
    }
}
</style>
