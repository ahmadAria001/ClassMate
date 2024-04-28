import { z } from "zod";

export const deleteSchema = z.object({
    "id": z.coerce.string(),
    "status": z.coerce.string(),
})

export type DeleteSchema = z.infer<typeof deleteSchema>;
