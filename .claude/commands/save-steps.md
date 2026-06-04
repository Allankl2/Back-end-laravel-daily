# save-steps

Salva o passo a passo do que foi construído nesta sessão na memória do projeto.

## Instruções

Ao ser invocado com `/save-steps <nome-da-feature>`, crie ou atualize o arquivo de memória em:

```
C:\Users\allan\.claude\projects\C--Users-allan-Desktop-php-api-Laravel-Meu-pessoal\memory\steps_<nome-da-feature>.md
```

O arquivo deve seguir este formato:

```markdown
---
name: steps-<nome-da-feature>
description: Passo a passo de construção de <nome-da-feature>
metadata:
  type: project
---

## O que foi construído
<descrição resumida>

## Arquivos criados / modificados (em ordem)
1. `caminho/do/arquivo.php` — o que foi feito
2. `caminho/do/outro.php` — o que foi feito

## Decisões tomadas
- **Decisão:** <o que foi decidido> **Motivo:** <por quê>

## Padrão seguido
<Controller → Service → Repository, ou o que se aplicar>
```

Após criar o arquivo, adicione uma linha de entrada no índice:

```
C:\Users\allan\.claude\projects\C--Users-allan-Desktop-php-api-Laravel-Meu-pessoal\memory\MEMORY.md
```

Formato da linha:
```
- [Steps: <nome-da-feature>](steps_<nome-da-feature>.md) — passo a passo da construção de <nome-da-feature>
```

Se nenhum argumento for passado, use o contexto da conversa atual para inferir o nome da feature.
