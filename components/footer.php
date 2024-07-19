<footer>
    <section>
        <div data-dropdown="lang">
            <div class="setting-description" data-dropdown-description="false">
                <div class="setting-description-text">
                    <h10><?= $tr->translate("Langue") ?></h10>
                </div>
            </div>
            <button type="button" class="no-style wrapper-dropdown" id="dropdown-lang">
                <span class="selected-display" id="destination"><?= ucfirst($lang) ?></span>
                <svg class="arrow transition-all ml-auto rotate-180" id="drp-arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 14.5l5-5 5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <ul class="dropdown">
                    <?php foreach ($languageArray as $language): ?>
                        <li class="item"><?= ucfirst($language) ?></li>
                    <?php endforeach; ?>
                </ul>
            </button>
        </div>
    </section>
</footer>
<div id="copyright">
    <div class="divider"></div>
    <p>
        By <a href="https://portfolio-psi-azure-25.vercel.app" target="_blank">Da Silva Pinto Kevin</a>. The source code is licensed &copy; <?= date("Y") ?>.
    </p>
</div>

<?php if(isset($splide)) echo '<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>'; ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="./js/script.js"></script>
</body>
</html>