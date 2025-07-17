# Drupal Modules

Este repositório contém versões modificadas de módulos oficiais do Drupal.  
As alterações incluem correções, ajustes de dependências ou modificações específicas.

> **Atenção:** Cada branch representa **um único módulo**.  
> **Nunca** faça merge entre branches. Cada uma deve permanecer isolada com o nome original do módulo.


## Objetivo

Facilitar o uso de módulos Drupal personalizados a partir de um único repositório Git, mantendo controle de versão e compatibilidade via Composer.


## Como importar um módulo para este repositório

Siga os passos abaixo para clonar, renomear a branch e empurrar para este repositório:

```
git clone GIT_MODULO_ORIGINAL
cd PATH_MODULO_ORIGINAL
git checkout VERSAO_ESPECIFICA
git branch -m NOME_ORIGINAL_MODULO_COMPLETO
git remote remove origin
git remote add origin https://github.com/f5-web-design/drupalmodules.git
git push origin NOME_ORIGINAL_MODULO_COMPLETO
```


## Como usar um módulo deste repositório no seu projeto Drupal

1. **Adicione o repositório no seu `composer.json`:**

```
"repositories": {
  "MODULE_NAME": {
    "type": "package",
    "package": {
      "name": "f5/MODULE_NAME",
      "version": "1.0.0",
      "type": "drupal-module",
      "source": {
        "url": "https://github.com/f5-web-design/drupalmodules.git",
        "type": "git",
        "reference": "BRANCH_NAME"
      }
    }
  }
}
```

2. **Instale o módulo com o Composer:**

```
lando composer require f5/MODULE_NAME
```
