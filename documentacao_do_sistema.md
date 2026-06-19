# 📖 SGM - Manual do Usuário (Sistema de Gestão de Manutenção)

Bem-vindo ao manual de uso do **SGM**. Este documento foi criado para explicar de forma simples e direta como o sistema funciona, sem jargões técnicos. Aqui você entenderá o caminho que um chamado percorre desde o momento em que um problema é relatado até o momento de sua solução.

---

## 👥 Perfis de Usuário
O sistema foi desenhado para conectar três tipos de pessoas, cada uma com uma responsabilidade diferente no processo. 

### 1. 🧑‍💼 Solicitante (Colaborador)
É qualquer pessoa que utiliza o espaço físico da instituição e identifica que algo precisa de conserto (ex: um ar-condicionado quebrado, uma lâmpada queimada, um vazamento).
**O que ele pode fazer:**
* Abrir novos chamados, descrevendo o problema, indicando o local exato (Bloco e Ambiente) e anexando uma foto inicial.
* Acompanhar pelo seu painel pessoal se o chamado dele já foi lido, se está sendo consertado ou se já foi finalizado.

### 2. 👩‍💻 Gestor / Administrador
É o "maestro" da manutenção. Ele não suja as mãos para consertar, mas organiza tudo. 
**O que ele pode fazer:**
* Recebe todas as novas solicitações.
* Lê os chamados, entende a gravidade e **atribui uma prioridade** (Baixa, Média, Alta ou Crítica).
* Estipula um prazo para a resolução.
* **Designa o chamado para o Técnico** que for mais adequado para aquela tarefa.
* Acompanha todo o fluxo da operação pelo Dashboard (painel geral), visualizando gráficos, filas de trabalho e avaliando a produtividade da equipe em tempo real.
* *(Apenas Administrador)*: Pode cadastrar novos blocos, ambientes, tipos de serviço e gerenciar usuários do sistema.

### 3. 🛠️ Técnico
É o profissional responsável por colocar a mão na massa e consertar o problema.
**O que ele pode fazer:**
* Visualiza a sua **"Fila de Atendimento"**, onde aparecem todos os chamados que o Gestor enviou exclusivamente para ele.
* A fila mostra claramente quais tarefas são mais urgentes pelas cores.
* Após ir até o local e consertar o problema, ele deve registrar a conclusão no sistema.
* **Para concluir uma tarefa, é obrigatório:** Enviar uma foto como evidência de que o serviço foi finalizado e registrar a data e horário exatos em que a tarefa foi concluída.

---

## 🔄 Fluxo de Vida de um Chamado
Como funciona a "jornada" de um problema dentro do sistema? Acompanhe o passo a passo:

> [!NOTE]
> **PASSO 1: Abertura (Status: `Aberto`)**
> O **Solicitante** vê uma torneira vazando. Ele entra no sistema, vai em "Nova Solicitação", seleciona o Banheiro Principal, tira uma foto da torneira e clica em enviar. O chamado nasce.

> [!IMPORTANT]
> **PASSO 2: Triagem e Designação (Status: Muda para `Em Atendimento`)**
> O **Gestor** vê o chamado da torneira na sua tela. Ele decide que isso é de prioridade **Alta** (para não desperdiçar água). Ele escolhe o encanador (Técnico X) na lista e atribui a tarefa a ele. O chamado sai da fila geral e vai para a fila particular do Técnico X.

> [!TIP]
> **PASSO 3: Execução e Conclusão (Status: Muda para `Concluído`)**
> O **Técnico X** olha sua tela (que se atualiza sozinha) e vê a tarefa da torneira. Ele pega suas ferramentas, vai até o banheiro e conserta a torneira. No sistema, ele clica em **"Concluir"**, informa que horas acabou o serviço, anexa uma foto da torneira novinha funcionando sem vazar, e salva.

> [!TIP]
> **PASSO 4: Fim da Jornada**
> O **Solicitante** recebe a informação no seu painel de que o chamado foi finalizado e o problema resolvido. Os números de produtividade do **Gestor** sobem (mais um chamado resolvido no dia!).

---

## ⚙️ Principais Funcionalidades Visuais

* **Atualização em Tempo Real ("Silenciosa")**: Os painéis de todo mundo atualizam os números e as filas de tarefas automaticamente a cada 2 minutos. Ninguém precisa ficar apertando F5. E se alguém estiver digitando um texto longo na tela, a tela não vai piscar nem apagar o que a pessoa está escrevendo.
* **Layout Responsivo e Limpo**: Os cartões de chamados e o menu lateral se adaptam a qualquer tela (podendo ser usados no computador da sala do gestor ou no celular do técnico no meio do pátio).
* **Filtros e Relatórios**: O Gestor consegue filtrar facilmente se quer ver apenas os chamados concluídos de hoje ou focar nos que estão pendentes e críticos.
