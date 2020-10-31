var fs = require('fs');

/**
 * This file exports the content of your website, as a bunch of concatenated
 * Markdown files. By doing this explicitly, you can control the order
 * of content without any level of abstraction.
 *
 * Using the brfs module, fs.readFileSync calls in this file are translated
 * into strings of those files' content before the file is delivered to a
 * browser: the content is read ahead-of-time and included in bundle.js.
 */
module.exports =
  '# Getting Started\n' +
  fs.readFileSync('./content/introduction.md', 'utf8') + '\n' +
  '# HTTP\n' +
  fs.readFileSync('./content/requests.md', 'utf8') + '\n' +
  '# Endpoints\n' +
  fs.readFileSync('./content/endpoints.md', 'utf8') + '\n'+
  '# Relationships (sana all)\n' +
  fs.readFileSync('./content/relationships.md', 'utf8') + '\n'+
  '# Payments\n' +
  fs.readFileSync('./content/payments.md', 'utf8') + '\n';
