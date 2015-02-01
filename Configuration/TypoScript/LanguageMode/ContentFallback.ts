# Recommended. The system will always operate with the selected language
# even if the page is not translated with a page overlay record. This will
# keep menus etc. translated. However, the content on the page can still
# fall back to another language, defined by the value of this keyword,
# e.g. "content_fallback ; 1,0" to fall back to the content of
# sys_language_uid 1 and if that is not present either, to default (0).
config.sys_language_mode = content_fallback
