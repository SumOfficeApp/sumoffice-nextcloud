#!/bin/sh
# Сборка архива выпуска для Nextcloud App Store — только так, руками не собирать.
#
# ЧТО БЫЛО, найдено проверкой дохождения 26.09.2026 на чистом Nextcloud 31:
#   occ app:install sumoffice → «Error: Extracted app sumoffice has more than 1 folder»
# То есть карточка в магазине живая, выпуск принят, а поставить приложение
# нельзя НИГДЕ. Причина: `tar` на маке кладёт расширенные атрибуты отдельными
# записями `._имя` (AppleDouble). На маке они при распаковке скрыты, на Linux
# это настоящие файлы, и рядом с `sumoffice` появляется `._sumoffice` —
# Nextcloud видит две записи на верхнем уровне и отказывается ставить.
#
# Отсюда COPYFILE_DISABLE=1 и исключения, а следом — проверка на Linux.
set -e
V="${1:?версия, например 0.1.4}"
cd "$(dirname "$0")"
ARCH="/tmp/sumoffice-$V.tar.gz"
rm -f "$ARCH"
COPYFILE_DISABLE=1 tar --no-xattrs --no-mac-metadata \
  --exclude '._*' --exclude '.DS_Store' -czf "$ARCH" sumoffice

# Застава: на верхнем уровне должна остаться ровно одна запись, и проверяем это
# распаковкой в Linux, а не на маке — мак прячет ровно те записи, что и ломают.
N=$(docker run --rm -v "$ARCH:/a.tar.gz:ro" alpine sh -c 'mkdir /x && tar xzf /a.tar.gz -C /x && ls -A /x | wc -l')
if [ "$N" -ne 1 ]; then
  echo "АРХИВ НЕГОДЕН: на верхнем уровне $N записей вместо одной" >&2
  docker run --rm -v "$ARCH:/a.tar.gz:ro" alpine sh -c 'mkdir /x && tar xzf /a.tar.gz -C /x && ls -A /x' >&2
  exit 1
fi

K=/Users/tester/Documents/sum-private/keys/nextcloud-appstore/sumoffice.key
openssl dgst -sha512 -sign "$K" "$ARCH" | openssl base64 -A > "/tmp/sumoffice-$V.sig"
echo "архив: $ARCH"
echo "подпись: /tmp/sumoffice-$V.sig"
echo "на верхнем уровне записей: $N — годен"
