package utility

import (
	"go/ast"
	"go/parser"
	"go/token"
	"strings"
)

func GetConstants(pkgImportPath string) (map[string]string, error) {
	// pkgImportPath example  ./services/names
	constants := map[string]string{}

	fset := token.NewFileSet()

	pkgs, err := parser.ParseDir(fset, pkgImportPath, nil, parser.ParseComments)
	if err != nil {
		return nil, err
	}

	for _, pkg := range pkgs {
		for _, file := range pkg.Files {
			for _, decl := range file.Decls {
				if genDecl, ok := decl.(*ast.GenDecl); ok && genDecl.Tok == token.CONST {
					for _, spec := range genDecl.Specs {
						if valueSpec, ok := spec.(*ast.ValueSpec); ok {
							for i, ident := range valueSpec.Names {
								actionSpec, ok := valueSpec.Values[i].(*ast.BasicLit)
								if ok {
									constants[strings.ReplaceAll(ident.Name, `"`, "")] = strings.ReplaceAll(actionSpec.Value, `"`, "")
								}

							}
						}
					}
				}
			}
		}
	}

	return constants, nil
}
