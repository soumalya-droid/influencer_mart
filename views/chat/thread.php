
<?php include __DIR__ . '/../partials/header.php'; ?>
<h1>Chat</h1>
<div id="chat" class="chat-box"></div>
<form id="chatForm">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
  <input type="hidden" id="chat_id" value="<?= (int)$chat_id ?>">
  <input id="msg" placeholder="Type a message..." autocomplete="off">
  <button class="btn" type="submit">Send</button>
</form>
<script>
  const chatId = document.getElementById('chat_id').value;
  let lastId = 0;
  async function poll(){
    const res = await fetch('<?= base_url('chat/poll') ?>?chat_id='+chatId+'&after='+lastId);
    const data = await res.json();
    const box = document.getElementById('chat');
    data.forEach(m => {
      lastId = Math.max(lastId, parseInt(m.id));
      const div = document.createElement('div');
      div.className='msg';
      div.textContent = m.sender_name + ': ' + m.body;
      box.appendChild(div);
      box.scrollTop = box.scrollHeight;
    });
  }
  setInterval(poll, 1500);
  poll();
  document.getElementById('chatForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const body = new URLSearchParams();
    body.append('csrf', document.querySelector('input[name=csrf]').value);
    body.append('chat_id', chatId);
    body.append('message', document.getElementById('msg').value);
    await fetch('<?= base_url('chat/send') ?>', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body});
    document.getElementById('msg').value = '';
  });
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
