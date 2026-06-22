export interface ResourceNodeData {
    id: number
    type: string
    label: string
    amount: number
    capacity: number
    productionRate: number
    speedLevel: number
    storageLevel: number
    upgradeSpeedCost: number
    upgradeStorageCost: number
}

export interface FactoryLineData {
    id: number
    name: string
    carModel: string
    carModelLabel: string
    status: 'idle' | 'running' | 'broken'
    queueCount: number
    completedBuffer: number
    speedLevel: number
    qualityMode: boolean
    progressPercent: number
    productionTimeSeconds: number
    upgradeSpeedCost: number
    materialsRequired: Record<string, number>
}

export interface TransportVehicleData {
    id: number
    vehicleType: string
    label: string
    status: 'idle' | 'in_transit' | 'returning'
    cargoCount: number
    cargoModel: string | null
    capacity: number
    speedLevel: number
    capacityLevel: number
    assignedShowroomId: number | null
    arrivesAt: string | null
    returnsAt: string | null
    transitTimeSeconds: number
    upgradeSpeedCost: number
    upgradeCapacityCost: number
}

export interface ShowroomInventoryData {
    id: number
    carModel: string
    carModelLabel: string
    quantity: number
    priceOverride: number | null
    basePrice: number
    effectivePrice: number
    salesRatePerMinute: number
}

export interface ShowroomData {
    id: number
    name: string
    locationTier: string
    staffLevel: number
    marketingLevel: number
    demandMultiplier: number
    upgradeStaffCost: number
    upgradeMarketingCost: number
    inventory: ShowroomInventoryData[]
}

export interface GameStateData {
    money: number
    lastTickedAt: string
    resourceNodes: ResourceNodeData[]
    factoryLines: FactoryLineData[]
    transportVehicles: TransportVehicleData[]
    showrooms: ShowroomData[]
}
