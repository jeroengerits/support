#!/usr/bin/env python3
"""
PHP API Documentation Generator for MkDocs.

This script generates API documentation from PHP source code using reflection
and PHPDoc comments. It creates markdown files that can be processed by MkDocs
with the mkdocstrings plugin.
"""

import os
import re
import sys
from pathlib import Path
from typing import Dict, List, Optional, Tuple
import json


class PHPDocParser:
    """Parser for PHPDoc comments in PHP files."""
    
    def __init__(self):
        self.current_file = ""
        self.current_class = ""
        self.current_method = ""
    
    def parse_file(self, file_path: Path) -> Dict:
        """Parse a PHP file and extract documentation."""
        self.current_file = str(file_path)
        
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        return self._parse_content(content)
    
    def _parse_content(self, content: str) -> Dict:
        """Parse PHP content and extract class/method documentation."""
        result = {
            'classes': [],
            'interfaces': [],
            'enums': [],
            'functions': [],
            'constants': []
        }
        
        # Find all classes
        class_pattern = r'class\s+(\w+)(?:\s+extends\s+(\w+))?(?:\s+implements\s+([^{]+))?\s*\{'
        for match in re.finditer(class_pattern, content, re.MULTILINE):
            class_name = match.group(1)
            extends = match.group(2) if match.group(2) else None
            implements = [i.strip() for i in match.group(3).split(',')] if match.group(3) else []
            
            class_doc = self._extract_phpdoc_before(content, match.start())
            
            result['classes'].append({
                'name': class_name,
                'extends': extends,
                'implements': implements,
                'docstring': class_doc,
                'methods': self._extract_methods(content, match.start(), match.end()),
                'properties': self._extract_properties(content, match.start(), match.end())
            })
        
        # Find all interfaces
        interface_pattern = r'interface\s+(\w+)(?:\s+extends\s+([^{]+))?\s*\{'
        for match in re.finditer(interface_pattern, content, re.MULTILINE):
            interface_name = match.group(1)
            extends = [i.strip() for i in match.group(2).split(',')] if match.group(2) else []
            
            interface_doc = self._extract_phpdoc_before(content, match.start())
            
            result['interfaces'].append({
                'name': interface_name,
                'extends': extends,
                'docstring': interface_doc,
                'methods': self._extract_methods(content, match.start(), match.end())
            })
        
        # Find all enums
        enum_pattern = r'enum\s+(\w+)(?:\s+:\s+(\w+))?\s*\{'
        for match in re.finditer(enum_pattern, content, re.MULTILINE):
            enum_name = match.group(1)
            backing_type = match.group(2) if match.group(2) else None
            
            enum_doc = self._extract_phpdoc_before(content, match.start())
            
            result['enums'].append({
                'name': enum_name,
                'backing_type': backing_type,
                'docstring': enum_doc,
                'cases': self._extract_enum_cases(content, match.start(), match.end())
            })
        
        return result
    
    def _extract_phpdoc_before(self, content: str, position: int) -> str:
        """Extract PHPDoc comment before a given position."""
        # Look backwards for /** ... */ comment
        start = content.rfind('/**', 0, position)
        if start == -1:
            return ""
        
        end = content.find('*/', start)
        if end == -1:
            return ""
        
        return content[start:end + 2]
    
    def _extract_methods(self, content: str, class_start: int, class_end: int) -> List[Dict]:
        """Extract methods from a class or interface."""
        methods = []
        
        # Find method patterns within the class
        method_pattern = r'(?:public|private|protected)?\s*(?:static\s+)?function\s+(\w+)\s*\([^)]*\)\s*(?::\s*(\w+))?\s*\{'
        
        for match in re.finditer(method_pattern, content[class_start:class_end], re.MULTILINE):
            method_name = match.group(1)
            return_type = match.group(2) if match.group(2) else None
            
            # Get the actual position in the full content
            actual_pos = class_start + match.start()
            method_doc = self._extract_phpdoc_before(content, actual_pos)
            
            methods.append({
                'name': method_name,
                'return_type': return_type,
                'docstring': method_doc
            })
        
        return methods
    
    def _extract_properties(self, content: str, class_start: int, class_end: int) -> List[Dict]:
        """Extract properties from a class."""
        properties = []
        
        # Find property patterns
        property_pattern = r'(?:public|private|protected)?\s*(?:readonly\s+)?(?:\w+\s+)?\$(\w+)(?:\s*=\s*[^;]+)?;'
        
        for match in re.finditer(property_pattern, content[class_start:class_end], re.MULTILINE):
            property_name = match.group(1)
            
            # Get the actual position in the full content
            actual_pos = class_start + match.start()
            property_doc = self._extract_phpdoc_before(content, actual_pos)
            
            properties.append({
                'name': property_name,
                'docstring': property_doc
            })
        
        return properties
    
    def _extract_enum_cases(self, content: str, enum_start: int, enum_end: int) -> List[Dict]:
        """Extract cases from an enum."""
        cases = []
        
        # Find enum case patterns
        case_pattern = r'case\s+(\w+)(?:\s*=\s*[^,;]+)?[,;]'
        
        for match in re.finditer(case_pattern, content[enum_start:enum_end], re.MULTILINE):
            case_name = match.group(1)
            
            # Get the actual position in the full content
            actual_pos = enum_start + match.start()
            case_doc = self._extract_phpdoc_before(content, actual_pos)
            
            cases.append({
                'name': case_name,
                'docstring': case_doc
            })
        
        return cases


class APIDocumentationGenerator:
    """Generates API documentation from PHP source code."""
    
    def __init__(self, src_path: str, docs_path: str):
        self.src_path = Path(src_path)
        self.docs_path = Path(docs_path)
        self.parser = PHPDocParser()
    
    def generate_documentation(self):
        """Generate API documentation for all PHP files."""
        print(f"Generating API documentation from {self.src_path} to {self.docs_path}")
        
        # Create docs directory structure
        self.docs_path.mkdir(parents=True, exist_ok=True)
        
        # Find all PHP files
        php_files = list(self.src_path.rglob("*.php"))
        
        for php_file in php_files:
            if self._should_skip_file(php_file):
                continue
            
            print(f"Processing {php_file.relative_to(self.src_path)}")
            self._process_php_file(php_file)
    
    def _should_skip_file(self, file_path: Path) -> bool:
        """Check if a file should be skipped."""
        # Skip test files, vendor files, etc.
        skip_patterns = ['/tests/', '/vendor/', '/Test.php', '/test.php']
        return any(pattern in str(file_path) for pattern in skip_patterns)
    
    def _process_php_file(self, php_file: Path):
        """Process a single PHP file and generate documentation."""
        try:
            parsed = self.parser.parse_file(php_file)
            
            # Generate documentation for each component
            for class_info in parsed['classes']:
                self._generate_class_doc(class_info, php_file)
            
            for interface_info in parsed['interfaces']:
                self._generate_interface_doc(interface_info, php_file)
            
            for enum_info in parsed['enums']:
                self._generate_enum_doc(enum_info, php_file)
                
        except Exception as e:
            print(f"Error processing {php_file}: {e}")
    
    def _generate_class_doc(self, class_info: Dict, php_file: Path):
        """Generate documentation for a class."""
        relative_path = php_file.relative_to(self.src_path)
        doc_path = self.docs_path / relative_path.with_suffix('.md')
        
        # Create directory if it doesn't exist
        doc_path.parent.mkdir(parents=True, exist_ok=True)
        
        content = f"# {class_info['name']}\n\n"
        
        if class_info['docstring']:
            content += self._format_phpdoc(class_info['docstring']) + "\n\n"
        
        if class_info['extends']:
            content += f"**Extends:** `{class_info['extends']}`\n\n"
        
        if class_info['implements']:
            content += f"**Implements:** {', '.join([f'`{i}`' for i in class_info['implements']])}\n\n"
        
        # Add methods
        if class_info['methods']:
            content += "## Methods\n\n"
            for method in class_info['methods']:
                content += f"### {method['name']}()\n\n"
                if method['docstring']:
                    content += self._format_phpdoc(method['docstring']) + "\n\n"
                if method['return_type']:
                    content += f"**Returns:** `{method['return_type']}`\n\n"
        
        # Add properties
        if class_info['properties']:
            content += "## Properties\n\n"
            for prop in class_info['properties']:
                content += f"### ${prop['name']}\n\n"
                if prop['docstring']:
                    content += self._format_phpdoc(prop['docstring']) + "\n\n"
        
        # Write the file
        with open(doc_path, 'w', encoding='utf-8') as f:
            f.write(content)
    
    def _generate_interface_doc(self, interface_info: Dict, php_file: Path):
        """Generate documentation for an interface."""
        relative_path = php_file.relative_to(self.src_path)
        doc_path = self.docs_path / relative_path.with_suffix('.md')
        
        # Create directory if it doesn't exist
        doc_path.parent.mkdir(parents=True, exist_ok=True)
        
        content = f"# {interface_info['name']} Interface\n\n"
        
        if interface_info['docstring']:
            content += self._format_phpdoc(interface_info['docstring']) + "\n\n"
        
        if interface_info['extends']:
            content += f"**Extends:** {', '.join([f'`{i}`' for i in interface_info['extends']])}\n\n"
        
        # Add methods
        if interface_info['methods']:
            content += "## Methods\n\n"
            for method in interface_info['methods']:
                content += f"### {method['name']}()\n\n"
                if method['docstring']:
                    content += self._format_phpdoc(method['docstring']) + "\n\n"
                if method['return_type']:
                    content += f"**Returns:** `{method['return_type']}`\n\n"
        
        # Write the file
        with open(doc_path, 'w', encoding='utf-8') as f:
            f.write(content)
    
    def _generate_enum_doc(self, enum_info: Dict, php_file: Path):
        """Generate documentation for an enum."""
        relative_path = php_file.relative_to(self.src_path)
        doc_path = self.docs_path / relative_path.with_suffix('.md')
        
        # Create directory if it doesn't exist
        doc_path.parent.mkdir(parents=True, exist_ok=True)
        
        content = f"# {enum_info['name']} Enum\n\n"
        
        if enum_info['docstring']:
            content += self._format_phpdoc(enum_info['docstring']) + "\n\n"
        
        if enum_info['backing_type']:
            content += f"**Backing Type:** `{enum_info['backing_type']}`\n\n"
        
        # Add cases
        if enum_info['cases']:
            content += "## Cases\n\n"
            for case in enum_info['cases']:
                content += f"### {case['name']}\n\n"
                if case['docstring']:
                    content += self._format_phpdoc(case['docstring']) + "\n\n"
        
        # Write the file
        with open(doc_path, 'w', encoding='utf-8') as f:
            f.write(content)
    
    def _format_phpdoc(self, phpdoc: str) -> str:
        """Format PHPDoc comment as markdown."""
        if not phpdoc:
            return ""
        
        # Remove /** and */
        content = phpdoc.strip()
        if content.startswith('/**'):
            content = content[3:]
        if content.endswith('*/'):
            content = content[:-2]
        
        # Clean up whitespace
        lines = [line.strip() for line in content.split('\n')]
        lines = [line for line in lines if line and not line.startswith('*')]
        
        return '\n'.join(lines)


def main():
    """Main function."""
    if len(sys.argv) != 3:
        print("Usage: python gen_api.py <src_path> <docs_path>")
        sys.exit(1)
    
    src_path = sys.argv[1]
    docs_path = sys.argv[2]
    
    generator = APIDocumentationGenerator(src_path, docs_path)
    generator.generate_documentation()
    
    print("API documentation generation complete!")


if __name__ == "__main__":
    main()