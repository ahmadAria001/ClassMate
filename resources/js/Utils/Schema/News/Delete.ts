import { z } from 'zod'

export const deleteSchema = z.object({
    id: z.coerce.number().min(1),
})

export type DeleteSchema = z.infer<typeof deleteSchema>;



