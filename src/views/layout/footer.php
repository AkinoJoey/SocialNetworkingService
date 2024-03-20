<script src="/main.bundle.js"></script>
<?php if(isset($user) && $user->getEmailVerified()): ?>
    <script src="/loggedInUser.bundle.js"></script>
<?php endif; ?>
</body>

</html>