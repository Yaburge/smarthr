<?php
// FILE: pages/admin/chatbot.php
$employee_id = $_GET['id'] ?? null;
?>

<section class="section">
  <div class="chatbot-container">
      
    <div class="chat-body bg-white rounded shadow padding-50">

      <div class="chat-display flex-column" id="chat-display">
        <div class="row-fixed justify-left">
          <p class="secondary-status">👋 Hello! I'm SmartBot. Ask me anything about your employment!</p>
        </div>
      </div>

      <div class="chat-texts">
        <p class="primary-status shadow" onclick="askQuestion('What is my credit leave balance?')">Credit Leave</p>
        <p class="primary-status shadow" onclick="askQuestion('What is my monthly salary?')">Monthly Salary</p>
        <p class="primary-status shadow" onclick="askQuestion('What is my semi-monthly salary?')">Semi-monthly Salary</p>
        <p class="primary-status shadow" onclick="askQuestion('Tell me about BIR tax')">BIR Tax</p>
        <p class="primary-status shadow" onclick="askQuestion('What are the upcoming holidays?')">Holidays</p>
        <p class="primary-status shadow" onclick="askQuestion('What is my allowance?')">Allowance</p>
      </div>

      <form id="chatbot-form" class="input-container">
        <input type="hidden" name="employee_id" value="<?= $employee_id ?>" id="employee_id">
        <input type="text" name="question" id="chat-input" placeholder="Ask Smart Bot" autocomplete="off">
        <button type="submit" class="btn solidBtn"><i class="fa-solid fa-paper-plane"></i></button>
      </form>
    </div>

  </div>
</section>