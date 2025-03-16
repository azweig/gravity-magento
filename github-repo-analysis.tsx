"use client"

import { useState, useEffect } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Button } from "@/components/ui/button"
import { Folder, File, GitBranch, RefreshCw } from "lucide-react"

export default function GitHubRepoAnalyzer() {
  const [repoData, setRepoData] = useState<any>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [activeTab, setActiveTab] = useState("structure")

  useEffect(() => {
    async function fetchRepoData() {
      try {
        setLoading(true)

        // Fetch repository structure
        const response = await fetch("https://api.github.com/repos/azweig/gravity-magento/contents")

        if (!response.ok) {
          throw new Error("Failed to fetch repository data")
        }

        const data = await response.json()
        setRepoData(data)
      } catch (err) {
        setError(err instanceof Error ? err.message : "An unknown error occurred")
      } finally {
        setLoading(false)
      }
    }

    fetchRepoData()
  }, [])

  const renderFileTree = (files: any[]) => {
    return (
      <ul className="space-y-2 pl-5">
        {files.map((item) => (
          <li key={item.path} className="flex items-start">
            {item.type === "dir" ? (
              <Folder className="h-5 w-5 mr-2 text-blue-500" />
            ) : (
              <File className="h-5 w-5 mr-2 text-gray-500" />
            )}
            <span>{item.name}</span>
          </li>
        ))}
      </ul>
    )
  }

  const renderReorganizationPlan = () => {
    return (
      <div className="space-y-4">
        <div className="p-4 border rounded-md bg-amber-50">
          <h3 className="font-medium mb-2">Plan de Reorganización</h3>
          <p>Para unificar la estructura en una sola carpeta raíz:</p>
          <ol className="list-decimal pl-5 space-y-2 mt-2">
            <li>
              Mover los archivos de <code>plugin/Quickcomm/Gravity</code> a una nueva carpeta{" "}
              <code>Quickcomm_Gravity</code> en la raíz
            </li>
            <li>
              Mantener la carpeta <code>Omnipro</code> como está
            </li>
            <li>
              Eliminar la carpeta <code>plugin</code> vacía
            </li>
          </ol>
        </div>

        <div className="p-4 border rounded-md bg-green-50">
          <h3 className="font-medium mb-2">Estructura Final Propuesta</h3>
          <ul className="space-y-1 pl-5">
            <li className="flex items-center">
              <Folder className="h-5 w-5 mr-2 text-blue-500" />
              <span>Omnipro/</span>
            </li>
            <li className="flex items-center">
              <Folder className="h-5 w-5 mr-2 text-blue-500" />
              <span>Quickcomm_Gravity/</span>
            </li>
          </ul>
        </div>

        <div className="p-4 border rounded-md bg-blue-50">
          <h3 className="font-medium mb-2">Comandos Git para la Reorganización</h3>
          <pre className="bg-gray-800 text-white p-3 rounded-md text-sm overflow-x-auto">
            <code>
              git checkout -b reorganize-structure{"\n"}
              git mv plugin/Quickcomm/Gravity Quickcomm_Gravity{"\n"}
              git rm -r plugin{"\n"}
              git commit -m "Reorganizar estructura: mover Quickcomm/Gravity a la raíz"{"\n"}
              git push origin reorganize-structure
            </code>
          </pre>
        </div>
      </div>
    )
  }

  return (
    <Card className="w-full">
      <CardHeader>
        <CardTitle className="flex items-center">
          <GitBranch className="h-5 w-5 mr-2" />
          Análisis del Repositorio: azweig/gravity-magento
        </CardTitle>
        <CardDescription>Análisis de la estructura actual y plan de reorganización</CardDescription>
      </CardHeader>
      <CardContent>
        <Tabs defaultValue="structure" value={activeTab} onValueChange={setActiveTab}>
          <TabsList className="mb-4">
            <TabsTrigger value="structure">Estructura Actual</TabsTrigger>
            <TabsTrigger value="plan">Plan de Reorganización</TabsTrigger>
          </TabsList>

          <TabsContent value="structure">
            {loading ? (
              <div className="flex items-center justify-center p-8">
                <RefreshCw className="h-6 w-6 animate-spin text-gray-500 mr-2" />
                <span>Cargando estructura del repositorio...</span>
              </div>
            ) : error ? (
              <div className="p-4 text-red-500 border border-red-200 rounded-md">Error: {error}</div>
            ) : (
              <div className="space-y-4">
                <div className="p-4 border rounded-md">
                  <h3 className="font-medium mb-2">Estructura Actual</h3>
                  {repoData && renderFileTree(repoData)}
                </div>

                <div className="p-4 border rounded-md bg-yellow-50">
                  <h3 className="font-medium mb-2">Observaciones</h3>
                  <ul className="list-disc pl-5 space-y-1">
                    <li>Tienes dos módulos de Magento separados</li>
                    <li>
                      El módulo <code>Omnipro</code> está en la raíz
                    </li>
                    <li>
                      El módulo <code>Quickcomm/Gravity</code> está anidado dentro de <code>plugin/</code>
                    </li>
                    <li>Ambos módulos tienen estructuras similares (etc, Helper, Model, Observer, Plugin)</li>
                  </ul>
                </div>
              </div>
            )}
          </TabsContent>

          <TabsContent value="plan">{renderReorganizationPlan()}</TabsContent>
        </Tabs>

        <div className="mt-6 flex justify-end">
          <Button onClick={() => setActiveTab("plan")} variant="default">
            Ver Plan de Reorganización
          </Button>
        </div>
      </CardContent>
    </Card>
  )
}

