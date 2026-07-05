<?php $qrLink = \App\Models\Setting::get('donate_qr_link', ''); ?>

<div class="glass-card rounded-2xl p-5 border border-navy-700/20 text-center">
    
    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500/20 to-pink-500/20 flex items-center justify-center mx-auto mb-3">
        <svg class="w-6 h-6 text-rose-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
    </div>
    
    <h3 class="font-heading font-bold text-base text-text-primary mb-1">Support At Omni</h3>
    <p class="text-[11px] text-text-muted leading-relaxed mb-4">Help keep independent journalism alive. Even a small contribution makes a difference.</p>

    
    <?php if($qrLink): ?>
    <div class="bg-white rounded-xl p-3 inline-block mb-3" id="widget-qrcode-container"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new QRCode(document.getElementById("widget-qrcode-container"), {
                text: "<?php echo e($qrLink); ?>",
                width: 96, // 24 * 4 (Tailwind w-24)
                height: 96,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.M
            });
        });
    </script>
    <p class="text-[10px] text-text-muted mb-3">Scan with any UPI app</p>
    <?php endif; ?>

    <a href="<?php echo e(url('/donate')); ?>" class="block w-full py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-electric to-cyan-glow hover:from-electric-light hover:to-cyan-glow transition-all shadow-lg shadow-electric/20 hover:shadow-electric/40 hover:-translate-y-0.5">
        Contribute Now →
    </a>
    
    <p class="text-[10px] text-text-muted mt-2">₹1,000+ gets a shoutout! ✨</p>
</div>
<?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/partials/donate-widget.blade.php ENDPATH**/ ?>