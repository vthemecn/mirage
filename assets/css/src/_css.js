const fs = require('fs');
const path = require('path');

// 监视的目录和文件路径
const SRC_CSS_DIR = './assets/css/src';
const MAIN_CSS_FILE = './assets/css/src/style.css';
const OUTPUT_CSS_FILE = './assets/css/style.css';

// 创建输出目录（如果不存在）
const outputDir = path.dirname(OUTPUT_CSS_FILE);
if (!fs.existsSync(outputDir)) {
  fs.mkdirSync(outputDir, { recursive: true });
}

/**
 * 解析CSS文件中的@import语句
 * @param {string} cssContent CSS内容
 * @returns {Array<{fullPath: string, importPath: string}>} import信息数组
 */
function parseImports(cssContent, baseDir) {
  const importRegex = /@import\s+(?:url\()?(?:"|')([^"']+)(?:"|')\)?;?/g;
  const imports = [];
  let match;

  while ((match = importRegex.exec(cssContent)) !== null) {
    const importPath = match[1];
    const fullPath = path.resolve(baseDir, importPath);
    imports.push({
      fullPath,
      importPath
    });
  }

  return imports;
}

/**
 * 递归处理CSS文件及其@import
 * @param {string} filePath 文件路径
 * @param {Set} visited 已访问文件集合，防止循环引用
 * @returns {string} 处理后的CSS内容
 */
function processCSSFile(filePath, visited = new Set()) {
  if (visited.has(filePath)) {
    return '';
  }

  visited.add(filePath);

  try {
    let content = fs.readFileSync(filePath, 'utf8');
    const imports = parseImports(content, path.dirname(filePath));

    // 从后向前替换@import语句，确保位置正确
    for (let i = imports.length - 1; i >= 0; i--) {
      const importInfo = imports[i];
      const importedContent = processCSSFile(importInfo.fullPath, visited);
      
      // 构造正则表达式匹配@import语句
      const escapedImportPath = importInfo.importPath.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      const importRegex = new RegExp(`@import\\s+(?:url\\()?["']${escapedImportPath}["']\\)?;?`, 'g');
      
      content = content.replace(importRegex, importedContent);
    }

    return content;
  } catch (err) {
    console.error(`Error processing file ${filePath}:`, err.message);
    return '';
  }
}

/**
 * 构建CSS文件
 */
function buildCSS() {
  console.log('Building CSS...');
  
  try {
    const mergedCSS = processCSSFile(path.resolve(MAIN_CSS_FILE));
    
    // 确保输出目录存在
    const outputDir = path.dirname(OUTPUT_CSS_FILE);
    if (!fs.existsSync(outputDir)) {
      fs.mkdirSync(outputDir, { recursive: true });
    }
    
    fs.writeFileSync(OUTPUT_CSS_FILE, mergedCSS, 'utf8');
    console.log(`CSS built successfully: ${OUTPUT_CSS_FILE}`);
  } catch (err) {
    console.error('Error building CSS:', err.message);
  }
}

/**
 * 监视文件变化
 */
function watchFiles() {
  // 初始构建
  buildCSS();

  // 使用fs.watch监视整个目录
  const watchers = [];
  
  function watchDirectory(dir) {
    const watcher = fs.watch(dir, { recursive: false }, (eventType, filename) => {
      if (filename && filename.endsWith('.css')) {
        console.log(`File changed: ${path.join(dir, filename)}`);
        buildCSS();
      }
    });
    
    watcher.on('error', (err) => {
      console.error(`Watcher error for directory ${dir}:`, err.message);
    });
    
    watchers.push(watcher);
    
    // 递归监视子目录
    const items = fs.readdirSync(dir);
    items.forEach(item => {
      const fullPath = path.join(dir, item);
      const stat = fs.statSync(fullPath);
      if (stat.isDirectory()) {
        watchDirectory(fullPath);
      }
    });
  }
  
  try {
    watchDirectory(SRC_CSS_DIR);
    console.log(`Watching CSS files in ${SRC_CSS_DIR}...`);
  } catch (err) {
    console.error('Error setting up watchers:', err.message);
  }
  
  // 优雅关闭
  process.on('SIGINT', () => {
    console.log('Stopping watchers...');
    watchers.forEach(watcher => watcher.close());
    process.exit(0);
  });
}

// 启动监视器
watchFiles();

