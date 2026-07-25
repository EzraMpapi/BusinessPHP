<div class="space-y-4">
  <div class="section-header"><h2>AI Assistant</h2>
    <button class="btn btn-secondary" onclick="clearChat()"><i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Clear</button></div>
  <div class="grid-3" style="gap:12px">
    <div class="card text-center cursor-pointer hover:border-green-300" onclick="quickPrompt('Summarize today sales')">
      <i data-lucide="bar-chart-3" class="w-5 h-5 text-[#16A34A] mx-auto mb-1"></i>
      <p class="text-[11.5px] font-semibold text-[#111827]">Sales Summary</p>
      <p class="text-[10px] text-slate-400">Today's performance</p></div>
    <div class="card text-center cursor-pointer hover:border-blue-300" onclick="quickPrompt('List overdue invoices and total amount outstanding')">
      <i data-lucide="file-text" class="w-5 h-5 text-blue-500 mx-auto mb-1"></i>
      <p class="text-[11.5px] font-semibold text-[#111827]">Outstanding AR</p>
      <p class="text-[10px] text-slate-400">Overdue invoices</p></div>
    <div class="card text-center cursor-pointer hover:border-purple-300" onclick="quickPrompt('Which products need restocking urgently?')">
      <i data-lucide="package" class="w-5 h-5 text-purple-500 mx-auto mb-1"></i>
      <p class="text-[11.5px] font-semibold text-[#111827]">Inventory Check</p>
      <p class="text-[10px] text-slate-400">Restock alerts</p></div>
  </div>
  <div class="card p-0 flex flex-col" style="height:420px">
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3"></div>
    <div class="border-t border-slate-100 p-3 flex gap-2">
      <input id="chat-input" class="bs-input flex-1" placeholder="Ask anything about your business..." onkeydown="if(event.key==='Enter')sendMsg()"/>
      <button class="btn btn-primary" onclick="sendMsg()"><i data-lucide="send" class="w-3.5 h-3.5"></i></button>
    </div>
  </div>
</div>
<script>
const chatEl=()=>document.getElementById('chat-messages');
function addMsg(role,text){
  const isUser=role==='user';
  chatEl().innerHTML+=`<div class="flex ${isUser?'justify-end':''}">
    <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-[13px] leading-relaxed ${isUser?'bg-[#16A34A] text-white':'bg-slate-100 text-[#111827]'}">
      ${text}</div></div>`;
  chatEl().scrollTop=chatEl().scrollHeight;
}
function addBotMsg(text){addMsg('bot',text);}
addBotMsg('Hello! I am your BusinessSphere AI assistant. Ask me about sales, inventory, invoices, staff, or any business data. Try the quick actions above.');
async function sendMsg(){
  const inp=document.getElementById('chat-input');
  const msg=inp.value.trim();if(!msg)return;
  addMsg('user',msg);inp.value='';
  addBotMsg('<span class="animate-pulse">Thinking...</span>');
  await new Promise(r=>setTimeout(r,1000));
  chatEl().lastElementChild.remove();
  const resp=generateResponse(msg);
  addBotMsg(resp);
}
function quickPrompt(msg){document.getElementById('chat-input').value=msg;sendMsg();}
function clearChat(){chatEl().innerHTML='';addBotMsg('Chat cleared. How can I help you?');}
function generateResponse(msg){
  const m=msg.toLowerCase();
  if(m.includes('invoice')||m.includes('ar')||m.includes('outstanding'))
    return 'Based on your sales data: <strong>5 invoices outstanding</strong> totaling <strong>TZS 16,200,000</strong>. Oldest: INV-2844 (Dar Tech Solutions, 12 days). Recommend sending payment reminders today.';
  if(m.includes('stock')||m.includes('inventory')||m.includes('restock'))
    return '<strong>1 item critically low:</strong> Cement 50kg (3 units, reorder at 20). Suggest placing PO with BuildRight Hardware for 100 bags immediately.';
  if(m.includes('sale')||m.includes('revenue'))
    return 'Today's sales: <strong>TZS 2,400,000</strong> across 3 invoices. Month-to-date: <strong>TZS 34,800,000</strong> (+12.4% vs last month). Top customer: Karibu Supermarket.';
  if(m.includes('staff')||m.includes('employee')||m.includes('hr'))
    return 'Current staff: <strong>5 employees</strong>. Active: 4, On Leave: 1 (Fatima Omar, returns July 31). Monthly payroll: <strong>TZS 3,230,000</strong>.';
  if(m.includes('customer')||m.includes('lead'))
    return 'Pipeline: <strong>5 leads</strong> worth TZS 30,000,000. Highest priority: Arusha Electronics (TZS 12M, Prospecting). Won this month: Moshi Fresh Foods.';
  return 'I can help you analyze sales, track inventory, manage invoices, and more. Try asking: "What are my top customers?", "Show overdue invoices", or "Which items need restocking?"';
}
</script>